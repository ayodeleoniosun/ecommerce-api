<?php

namespace App\Infrastructure\Services\Payments\Korapay;

use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Interfaces\ApiLogsTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Domain\Payment\Interfaces\PaymentGatewayServiceInterface;
use App\Infrastructure\Models\Payment\Integration\TransactionKoraCardPayment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;

class Service extends BaseService implements PaymentGatewayServiceInterface
{
    use Utility;

    public function __construct(
        public readonly Charge $charge,
        public readonly CardTransactionRepositoryInterface $cardTransactionRepository,
        public readonly ApiLogsTransactionRepositoryInterface $apiLogsTransactionRepository,
    ) {}

    /**
     * @throws ConnectionException
     */
    public function initiate(InitiateOrderPaymentDto $paymentDto): array
    {
        $transaction = $this->createTransaction($paymentDto->getPaymentId(), $paymentDto);

        $paymentDto->setReference($transaction->reference);

        $encryptedData = $this->encryptCardData($paymentDto->toJson());

        $response = $this->charge->initiate($encryptedData);

        $status = self::getKorapayStatus($response->data->status);

        DB::transaction(function () use ($transaction, $status, $response) {
            $transaction = $this->cardTransactionRepository->update($transaction->id, [
                'status' => $status,
                'gateway_response' => $response->data->response_message,
                'gateway_transaction_id' => $response->data->transaction_reference,
            ]);

            $this->apiLogsTransactionRepository->update($transaction->id, [
                'charge_response' => json_encode($response),
            ]);
        });

        return [
            'status' => $status,
            'fee' => $response->data->fee,
            'vat' => $response->data->vat,
            'auth_model' => $response->data->auth_model,
            'gateway' => 'korapay',
            'gateway_reference' => $response->data->transaction_reference,
            'gateway_response_message' => $response->data->response_message,
        ];
    }

    private function createTransaction(
        int $orderPaymentId,
        InitiateOrderPaymentDto $paymentDto,
    ): TransactionKoraCardPayment {
        return DB::transaction(function () use ($orderPaymentId, $paymentDto) {
            $transaction = $this->cardTransactionRepository->create($orderPaymentId, $paymentDto);

            $this->apiLogsTransactionRepository->create($transaction->id);

            return $transaction;
        });
    }

    public function verify(string $reference): array
    {
        return [];
    }
}
