<?php

namespace App\Infrastructure\Services\Payments\Flutterwave;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Dtos\InitiateCardPaymentDto;
use App\Domain\Payment\Dtos\PaymentAuthorizationDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Enums\PaymentStatusEnum;
use App\Domain\Payment\Enums\PaymentTypeEnum;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\PaymentGatewayIntegrationInterface;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\ApiLogsFlutterwaveCardPayment;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\TransactionFlutterwaveCardPayment;
use App\Infrastructure\Services\Payments\Flutterwave\Enum\AuthModelEnum;
use App\Infrastructure\Services\Payments\PaymentGatewayIntegration;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;

class FlutterwaveIntegration extends PaymentGatewayIntegration implements PaymentGatewayIntegrationInterface
{
    use UtilitiesTrait, Utility;

    public string $gateway = 'flutterwave';

    public function __construct(
        public readonly CardTransactionRepositoryInterface $cardTransactionRepository,
    ) {
        parent::__construct();
    }

    public function authorize(PaymentAuthorizationDto $paymentAuthorizationDto): PaymentResponseDto
    {
        // TODO: Implement authorize() method.
    }

    /**
     * @throws ConnectionException
     */
    public function initiate(InitiateCardPaymentDto $paymentDto): PaymentResponseDto
    {
        $transaction = $this->createTransaction($paymentDto);
        $response = $this->initializeCharge($paymentDto);

        if (! isset($response['data']['status'])) {
            return new PaymentResponseDto(
                status: PaymentStatusEnum::FAILED->value,
                paymentMethod: PaymentTypeEnum::CARD->value,
                reference: $paymentDto->getOrderPaymentReference(),
                responseMessage: $response['message'],
                gateway: $this->gateway
            );
        }

        $authModel = AuthModelEnum::label($response['data']['auth_model'])->value;
        $authMode = $response['meta']['authorization']['mode'];
        $status = self::getFlutterwaveChargeStatus($response['data']['status']);

        $this->updateChargeResponseInTransactionAndApiLog($transaction, $status, $response);

        $paymentResponseDto = new PaymentResponseDto(
            status: $status,
            paymentMethod: PaymentTypeEnum::CARD->value,
            reference: $paymentDto->getOrderPaymentReference(),
            responseMessage: $response['data']['processor_response'],
            authModel: $authModel,
            gateway: $this->gateway,
            amountCharged: $response['data']['charged_amount'],
            fee: $response['data']['app_fee'],
            vat: 0,
        );

        if (self::requiresRedirection($authMode)) {
            $paymentResponseDto->setRedirectionUrl($response['meta']['authorization']['redirect']);
        }

        return $paymentResponseDto;
    }

    private function createTransaction(InitiateCardPaymentDto $paymentDto): TransactionFlutterwaveCardPayment
    {
        return DB::transaction(function () use ($paymentDto) {
            $transaction = $this->cardTransactionRepository->create(
                TransactionFlutterwaveCardPayment::class,
                $paymentDto->toTransactionArray(),
            );

            $this->cardTransactionRepository->create(
                ApiLogsFlutterwaveCardPayment::class,
                ['transaction_id' => $transaction->id],
            );

            return $transaction->load('apiLog');
        });
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function initializeCharge(InitiateCardPaymentDto $paymentDto): array
    {
        $requestData = [
            'amount' => $paymentDto->getAmount(),
            'currency' => $paymentDto->getCurrency(),
            'card_number' => $paymentDto->getCard()->getNumber(),
            'cvv' => $paymentDto->getCard()->getCvv(),
            'expiry_month' => $paymentDto->getCard()->getExpiryMonth(),
            'expiry_year' => $paymentDto->getCard()->getExpiryYear(),
            'email' => $paymentDto->getCustomer()->getEmail(),
            'tx_ref' => $paymentDto->getGatewayReference(),
            'authorization' => [
                'mode' => 'pin',
                'pin' => $paymentDto->getCard()->getPin(),
            ],
        ];

        $encryptedData = $this->encryptCardData($requestData, $this->encryptionKey);

        return $this->request(
            method: 'POST',
            path: '/charges?type=card',
            payload: [
                'client' => $encryptedData,
            ],
            options: [
                'authorization' => $this->secretKey,
            ],
        );
    }

    private function updateChargeResponseInTransactionAndApiLog(
        Model $transaction,
        string $status,
        array $response,
    ): void {
        DB::transaction(function () use ($transaction, $status, $response) {
            $this->cardTransactionRepository->update(
                model: TransactionFlutterwaveCardPayment::class,
                data: [
                    'transaction_id' => $transaction->id,
                    'status' => $status,
                    'auth_model' => AuthModelEnum::label($response['data']['auth_model'])->value,
                    'gateway_response' => $response['data']['processor_response'],
                    'gateway_transaction_reference' => $response['data']['id'],
                ],
            );

            $this->cardTransactionRepository->update(
                model: ApiLogsFlutterwaveCardPayment::class,
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
    public function verify(string $reference): PaymentResponseDto
    {
        $response = $this->verifyCharge($reference);

        $responseStatus = $response['status'];

        if ($responseStatus === 'error') {
            $status = PaymentStatusEnum::FAILED->value;

            return new PaymentResponseDto(
                status: $status,
                paymentMethod: PaymentTypeEnum::CARD->value,
                reference: $response['data']['tx_ref'],
                responseMessage: $response['message']
            );
        }

        $transaction = $this->cardTransactionRepository->findByColumn(
            model: TransactionFlutterwaveCardPayment::class,
            field: 'gateway_transaction_reference',
            value: $reference,
        );

        $this->updateVerifyResponseInTransactionAndApiLog($transaction, $response['data']['status'], $response);

        $vat = $response['data']['amount'] - $response['data']['app_fee'] - $response['data']['amount_settled'];

        return new PaymentResponseDto(
            status: self::getFlutterwaveVerifyStatus($response['data']['status']),
            paymentMethod: PaymentTypeEnum::CARD->value,
            reference: $response['data']['tx_ref'],
            responseMessage: $response['data']['processor_response'],
            authModel: AuthModelEnum::label($response['data']['auth_model'])->value,
            amountCharged: $response['data']['amount'],
            fee: $response['data']['app_fee'],
            vat: $vat,
        );
    }

    /**
     * @throws ConnectionException
     */
    public function verifyCharge(string $reference): array
    {
        return $this->request(
            method: 'GET',
            path: '/transactions/'.$reference.'/verify',
            options: [
                'authorization' => $this->secretKey,
            ],
        );
    }

    private function updateVerifyResponseInTransactionAndApiLog(
        Model $transaction,
        string $status,
        array $response,
    ): void {
        DB::transaction(function () use ($transaction, $status, $response) {
            $this->cardTransactionRepository->update(
                model: TransactionFlutterwaveCardPayment::class,
                data: [
                    'transaction_id' => $transaction->id,
                    'status' => $status,
                    'gateway_response' => $response['data']['processor_response'],
                ],
            );

            $this->cardTransactionRepository->update(
                model: ApiLogsFlutterwaveCardPayment::class,
                data: [
                    'transaction_id' => $transaction->apiLog->id,
                    'verify_response' => json_encode($response),
                ],
            );
        });
    }
}
