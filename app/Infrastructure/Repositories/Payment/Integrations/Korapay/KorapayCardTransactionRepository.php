<?php

namespace App\Infrastructure\Repositories\Payment\Integrations\Korapay;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Constants\GatewayPrefixReference;
use App\Domain\Payment\Dtos\InitiateOrderPaymentDto;
use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Infrastructure\Models\Payment\Integration\TransactionKoraCardPayment;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class KorapayCardTransactionRepository extends BaseRepository implements CardTransactionRepositoryInterface
{
    use UtilitiesTrait;

    public function create(int $orderPaymentId, InitiateOrderPaymentDto $paymentDto): Model
    {
        return TransactionKoraCardPayment::create([
            'order_payment_id' => $orderPaymentId,
            'reference' => self::generateRandomCharacters(GatewayPrefixReference::KORAPAY->value),
            'currency' => $paymentDto->getCurrency(),
            'amount' => $paymentDto->getAmount(),
        ]);
    }

    public function update(int $transactionId, array $data): ?Model
    {
        $transaction = $this->findByColumn(TransactionKoraCardPayment::class, 'id', $transactionId);

        if (! $transaction) {
            return null;
        }

        $transaction->update($data);

        return $transaction;
    }
}
