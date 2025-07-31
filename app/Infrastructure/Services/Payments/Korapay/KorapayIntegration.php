<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Order\Enums\OrderStatusEnum;
use App\Domain\Payment\Dtos\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\AuthModelEnum;
use App\Domain\Payment\Enums\PaymentResponseMessageEnum;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
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
    public function initiate(InitiateCardPaymentDto $paymentDto): PaymentResponseDto
    {
        $transaction = $this->createTransaction($paymentDto);
        $response = $this->initializeCharge($paymentDto);

        $status = $response['data']['status'];

        $paymentResponseDto = new PaymentResponseDto(
            status: $status,
            paymentMethod: PaymentTypeEnum::CARD->value,
            reference: $paymentDto->getOrderPaymentReference(),
            responseMessage: $response['data']['response_message'],
            gateway: $this->gateway,
        );

        if ($status === PaymentStatusEnum::FAILED->value) {
            $this->updateTransactionAndApiLog($transaction, $status, $response);

            return $paymentResponseDto;
        }

        $authModel = $response['data']['auth_model'];

        $paymentResponseDto->setAuthModel($authModel);
        $paymentResponseDto->setAmountCharged($response['data']['amount_charged']);
        $paymentResponseDto->setFee($response['data']['fee']);
        $paymentResponseDto->setVat($response['data']['vat']);

        if (self::requiresAuthorization($authModel)) {
            $this->updateTransactionAndApiLog($transaction, $status, $response, updateTransactionOnly: true);

            if ($authModel === AuthModelEnum::THREE_DS->value) {
                $paymentResponseDto->setRedirectionUrl($response['data']['redirect_url']);
            }

            return $paymentResponseDto;
        }

        $status = self::getKorapayStatus($status);

        $this->updateTransactionAndApiLog($transaction, $status, $response);

        $paymentResponseDto->setStatus($status);

        return $paymentResponseDto;
    }

    private function createTransaction(InitiateCardPaymentDto $paymentDto): TransactionKoraCardPayment
    {
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

    /**
     * @throws ConnectionException
     */
    public function initializeCharge(InitiateCardPaymentDto $paymentDto): array
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
                    'auth_model' => $response['data']['auth_model'] ?? null,
                    'gateway_response' => $response['data']['response_message'],
                    'gateway_transaction_reference' => $response['data']['transaction_reference'] ?? null,
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

    /**
     * @throws ConnectionException
     */
    public function authorize(PaymentAuthorizationDto $paymentAuthorizationDto): PaymentResponseDto
    {
        $transaction = $this->cardTransactionRepository->findByColumn(
            TransactionKoraCardPayment::class,
            'order_payment_reference',
            $paymentAuthorizationDto->getReference(),
        );

        if (! $transaction) {
            return new PaymentResponseDto(
                status: OrderStatusEnum::FAILED->value,
                paymentMethod: PaymentTypeEnum::CARD->value,
                reference: $paymentAuthorizationDto->getReference(),
                responseMessage: PaymentResponseMessageEnum::TRANSACTION_NOT_FOUND->value,
                gateway: $this->gateway
            );
        }

        if (in_array($transaction->status, self::completedTransactionStatuses())) {
            return new PaymentResponseDto(
                status: OrderStatusEnum::FAILED->value,
                paymentMethod: PaymentTypeEnum::CARD->value,
                reference: $paymentAuthorizationDto->getReference(),
                responseMessage: PaymentResponseMessageEnum::TRANSACTION_ALREADY_COMPLETED->value,
                authModel: $transaction->auth_model,
                gateway: $this->gateway
            );
        }

        $paymentAuthorizationDto->setGatewayReference($transaction->gateway_transaction_reference);
        $paymentAuthorizationDto->setAuthModel($transaction->auth_model);

        $response = $this->authorizeCharge($paymentAuthorizationDto);

        if (! isset($response['data']['status'])) {
            return new PaymentResponseDto(
                status: PaymentStatusEnum::FAILED->value,
                paymentMethod: PaymentTypeEnum::CARD->value,
                reference: $paymentAuthorizationDto->getReference(),
                responseMessage: $response['message'],
                authModel: $transaction->auth_model,
                gateway: $this->gateway
            );
        }

        $status = self::getKorapayStatus($response['data']['status']);

        $this->updateTransactionAndApiLog($transaction, $status, $response);

        if ($status === PaymentStatusEnum::FAILED->value) {
            return new PaymentResponseDto(
                status: OrderStatusEnum::FAILED->value,
                paymentMethod: PaymentTypeEnum::CARD->value,
                reference: $paymentAuthorizationDto->getReference(),
                responseMessage: $response['data']['response_message'],
                authModel: $transaction->auth_model,
                gateway: $this->gateway,
            );
        }

        return new PaymentResponseDto(
            status: $status,
            paymentMethod: PaymentTypeEnum::CARD->value,
            reference: $paymentAuthorizationDto->getReference(),
            responseMessage: $response['data']['response_message'],
            authModel: $transaction->auth_model,
            gateway: $this->gateway,
            amountCharged: $response['data']['amount_charged'],
            fee: $response['data']['fee'],
            vat: $response['data']['vat'],
        );
    }

    /**
     * @throws ConnectionException
     */
    public function authorizeCharge(PaymentAuthorizationDto $paymentAuthorizationDto): array
    {
        return $this->request(
            method: 'POST',
            path: '/charges/card/authorize',
            payload: [
                'transaction_reference' => $paymentAuthorizationDto->getGatewayReference(),
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

    public function verify(string $reference)
    {
        return [];
    }
}
