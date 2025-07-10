<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Constants\GatewayPrefixReference;
use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\PaymentGatewayIntegrationInterface;
use App\Infrastructure\Models\Payment\Integration\Korapay\ApiLogsKoraCardPayment;
use App\Infrastructure\Models\Payment\Integration\Korapay\TransactionKoraCardPayment;
use App\Infrastructure\Services\Payments\PaymentGatewayIntegration;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;

class KorapayIntegration extends PaymentGatewayIntegration implements PaymentGatewayIntegrationInterface
{
    use UtilitiesTrait, Utility;

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

        $status = self::getKorapayStatus($response['data']['status']);

        DB::transaction(function () use ($transaction, $status, $response) {
            $this->cardTransactionRepository->update(
                model: TransactionKoraCardPayment::class,
                data: [
                    'transaction_id' => $transaction->id,
                    'status' => $status,
                    'gateway_response' => $response['data']['response_message'],
                    'gateway_transaction_id' => $response['data']['transaction_reference'],
                ],
            );

            $this->cardTransactionRepository->update(
                model: ApiLogsKoraCardPayment::class,
                data: [
                    'transaction_id' => $transaction->apiLog->id,
                    'charge_response' => json_encode($response),
                ],
            );
        });

        return new PaymentResponseDto(
            amountCharged: $response['data']['amount_charged'],
            fee: $response['data']['fee'],
            vat: $response['data']['vat'],
            status: $status,
            authModel: $response['data']['auth_model'],
            gateway: $this->gateway,
            reference: $response['data']['transaction_reference'],
            responseMessage: $response['data']['response_message']
        );
    }

    private function createTransaction(InitiateOrderPaymentDto $paymentDto): TransactionKoraCardPayment
    {
        $paymentDto->setReference(self::generateRandomCharacters(GatewayPrefixReference::KORAPAY->value));

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
    private function initializeCharge(InitiateOrderPaymentDto $paymentDto): array
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

    public function verify(string $reference): array
    {
        return [];
    }
}
