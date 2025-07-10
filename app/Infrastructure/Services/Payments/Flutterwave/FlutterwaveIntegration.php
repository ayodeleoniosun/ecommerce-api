<?php

namespace App\Infrastructure\Services\Payments\Flutterwave;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Constants\GatewayPrefixReference;
use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Dtos\PaymentResponseDto;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\PaymentGatewayIntegrationInterface;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\ApiLogsFlutterwaveCardPayment;
use App\Infrastructure\Models\Payment\Integration\Flutterwave\TransactionFlutterwaveCardPayment;
use App\Infrastructure\Services\Payments\PaymentGatewayIntegration;
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
                model: TransactionFlutterwaveCardPayment::class,
                data: [
                    'transaction_id' => $transaction->id,
                    'status' => $status,
                    'gateway_response' => $response['data']['response_message'],
                    'gateway_transaction_id' => $response['data']['transaction_reference'],
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

    private function createTransaction(InitiateOrderPaymentDto $paymentDto): TransactionFlutterwaveCardPayment
    {
        $paymentDto->setReference(self::generateRandomCharacters(GatewayPrefixReference::FLUTTERWAVE->value));

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
     */
    private function initializeCharge(InitiateOrderPaymentDto $paymentDto): array
    {
        $chargeToken = $this->encryptCardData(json_encode([
            'amount' => $paymentDto->getAmount(),
            'currency' => $paymentDto->getCurrency(),
            'card_number' => $paymentDto->getCard()->getNumber(),
            'cvv' => $paymentDto->getCard()->getCvv(),
            'expiry_month' => $paymentDto->getCard()->getExpiryMonth(),
            'expiry_year' => $paymentDto->getCard()->getExpiryYear(),
            'email' => $paymentDto->getCustomer()->getEmail(),
            'tx_ref' => $paymentDto->getReference(),
            'authorization' => [
                'mode' => 'pin',
                'pin' => $paymentDto->getCard()->getPin(),
            ],
        ]), $this->encryptionKey);

        return $this->request(
            method: 'POST',
            path: '/charges?type=card',
            payload: [
                'client' => 'Of8p6iJUVUezgvjUkjjJsP8aPd6CjHR3f9ptHiH5Q0+2h/FzHA/X1zPlDmRmH5v+GoLWWB4TqEojrKhZI38MSjbGm3DC8UPf385zBYEHZdgvQDsacDYZtFEruJqEWXmbvw9sUz+YwUHegTSogQdnXp7OGdUxPngiv6592YoL0YXa4eHcH1fRGjAimdqucGJPurFVu4sE5gJIEmBCXdESVqNPG72PwdRPfAINT9x1bXemI1M3bBdydtWvAx58ZE4fcOtWkD/IDi+o8K7qpmzgUR8YUbgZ71yi0pg5UmrT4YpcY2eq5i46Gg3L+fxFl4tauG9H4WBChF0agXtP4kjfhfYVD48N9Hrt',
            ],
            options: [
                'authorization' => $this->secretKey,
            ],
        );
    }

    public function verify(string $reference): array
    {
        // TODO: Implement verify() method.
    }
}
