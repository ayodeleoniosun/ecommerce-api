<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\GatewayPrefixReferenceEnum;
use App\Domain\Payment\Enums\PaymentErrorEnum;
use App\Domain\Payment\Enums\PaymentErrorTypeEnum;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\PaymentGatewayIntegrationInterface;
use App\Infrastructure\Models\Payment\Integration\Korapay\ApiLogsKoraCardPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\TransactionKoraCardPayment;
use App\Infrastructure\Services\Payments\PaymentGatewayIntegration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;

class KorapayIntegration extends PaymentGatewayIntegration implements PaymentGatewayIntegrationInterface
{
    use UtilitiesTrait, UtilityTrait;

    public string $gateway = 'korapay';

    public function __construct(
        public readonly CardTransactionRepositoryInterface $cardTransactionRepository,
    ) {
        parent::__construct();
    }

    /**
     * @throws ConnectionException
     */
    public function initiate(InitiateOrderPaymentDto $paymentDto): PaymentResponseDto
    {
        $transaction = $this->createTransaction($paymentDto);
        $response = $this->initializeCharge($paymentDto);

        $authModel = $response['data']['auth_model'];
        $status = $response['data']['status'];

        if (self::requiresAuthorization($authModel)) {
            $this->updateTransactionAndApiLog($transaction, $status, $response, updateTransactionOnly: true);

            $paymentResponseDto = new PaymentResponseDto(
                status: $status,
                authModel: $authModel,
                gateway: $this->gateway,
                reference: $response['data']['transaction_reference'],
                responseMessage: $response['data']['response_message']
            );

            if ($authModel === AuthModelEnum::THREE_DS->value) {
                $paymentResponseDto->setRedirectionUrl($response['data']['redirect_url']);
            }

            return $paymentResponseDto;
        }

        $status = self::getKorapayStatus($status);

        $this->updateTransactionAndApiLog($transaction, $status, $response);

        return new PaymentResponseDto(
            status: $status,
            authModel: $authModel,
            gateway: $this->gateway,
            reference: $response['data']['transaction_reference'],
            responseMessage: $response['data']['response_message'],
            amountCharged: $response['data']['amount_charged'],
            fee: $response['data']['fee'],
            vat: $response['data']['vat']
        );
    }

    /**
     * @throws ConnectionException
     */
    public function initializeCharge(InitiateOrderPaymentDto $paymentDto): array
    {
        $chargeData = $this->encryptCardData($paymentDto->toJson(), $this->encryptionKey);

        return $this->request(
            method: 'POST',
            path: '/charges/card',
            payload: [
                'charge_data' => $chargeData,
            ],
            options: [
                'authorization' => $this->secretKey,
            ],
        );
    }

    public function authorize(PaymentAuthorizationDto $paymentAuthorizationDto): PaymentResponseDto
    {
        $transaction = $this->cardTransactionRepository->findByColumn(
            TransactionKoraCardPayment::class,
            'gateway_transaction_reference',
            $paymentAuthorizationDto->getReference(),
        );

        if (! $transaction) {
            return new PaymentResponseDto(
                status: OrderStatusEnum::FAILED->value,
                authModel: $transaction->auth_model,
                gateway: $this->gateway,
                reference: $paymentAuthorizationDto->getReference(),
                responseMessage: PaymentErrorEnum::TRANSACTION_NOT_FOUND->value,
                errorType: PaymentErrorTypeEnum::TRANSACTION_NOT_FOUND->value
            );
        }

        if (in_array($transaction->status, self::completedTransactionStatuses())) {
            return new PaymentResponseDto(
                status: OrderStatusEnum::FAILED->value,
                authModel: $transaction->auth_model,
                gateway: $this->gateway,
                reference: $paymentAuthorizationDto->getReference(),
                responseMessage: PaymentErrorEnum::TRANSACTION_ALREADY_COMPLETED->value,
                errorType: PaymentErrorTypeEnum::TRANSACTION_ALREADY_COMPLETED->value
            );
        }

        $paymentAuthorizationDto->setAuthModel($transaction->auth_model);

        $response = $this->authorizeCharge($paymentAuthorizationDto);

        $status = self::getKorapayStatus($response['data']['status'] ?? null);

        if (! isset($response['data']['status'])) {
            return new PaymentResponseDto(
                status: $status,
                authModel: $transaction->auth_model,
                gateway: $this->gateway,
                reference: $transaction->gateway_transaction_reference,
                responseMessage: $response['message'],
                errorType: PaymentErrorTypeEnum::INVALID_REQUEST->value
            );
        }

        $this->updateTransactionAndApiLog($transaction, $status, $response);

        return new PaymentResponseDto(
            status: $status,
            authModel: $transaction->auth_model,
            gateway: $this->gateway,
            reference: $response['data']['transaction_reference'],
            responseMessage: $response['data']['response_message'],
            amountCharged: $response['data']['amount_charged'],
            fee: $response['data']['fee'],
            vat: $response['data']['vat'],
        );
    }

    public function authorizeCharge(PaymentAuthorizationDto $paymentAuthorizationDto): array
    {
        return $this->request(
            method: 'POST',
            path: '/charges/card/authorize',
            payload: [
                'transaction_reference' => $paymentAuthorizationDto->getReference(),
                'authorization' => self::getAuthorizationData(
                    $paymentAuthorizationDto->getAuthModel(),
                    $paymentAuthorizationDto->getAuthorization(),
                ),
            ],
            options: [
                'authorization' => $this->secretKey,
            ],
        );
    }

    public function verify(string $reference): array
    {
        return [];
    }

    private function createTransaction(InitiateOrderPaymentDto $paymentDto): TransactionKoraCardPayment
    {
        if (! $paymentDto->getReference()) {
            $paymentDto->setReference(self::generateRandomCharacters(GatewayPrefixReferenceEnum::KORAPAY->value));
        }

        return DB::transaction(function () use ($paymentDto) {
            $transaction = $this->cardTransactionRepository->create(
                TransactionKoraCardPayment::class,
                $paymentDto->toTransactionArray(),
            );

            $this->cardTransactionRepository->create(
                ApiLogsKoraCardPayment::class,
                ['transaction_id' => $transaction->id],
            );

            return $transaction->load('apiLog');
        });
    }

    private function updateTransactionAndApiLog(
        Model $transaction,
        string $status,
        array $response,
        bool $updateTransactionOnly = false,
    ): void {
        DB::transaction(function () use ($transaction, $status, $response, $updateTransactionOnly) {
            $this->cardTransactionRepository->update(
                model: TransactionKoraCardPayment::class,
                data: [
                    'transaction_id' => $transaction->id,
                    'status' => $status,
                    'auth_model' => $response['data']['auth_model'],
                    'gateway_response' => $response['data']['response_message'],
                    'gateway_transaction_reference' => $response['data']['transaction_reference'],
                ],
            );

            if ($updateTransactionOnly) {
                return;
            }

            $this->cardTransactionRepository->update(
                model: ApiLogsKoraCardPayment::class,
                data: [
                    'transaction_id' => $transaction->apiLog->id,
                    'charge_response' => json_encode($response),
                ],
            );
        });
    }
}
