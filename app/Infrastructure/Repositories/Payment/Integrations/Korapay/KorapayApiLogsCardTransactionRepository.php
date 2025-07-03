<?php

namespace App\Infrastructure\Repositories\Payment\Integrations\Korapay;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Domain\Payment\Interfaces\ApiLogsTransactionRepositoryInterface;
use App\Infrastructure\Models\Payment\Integration\ApiLogsKoraCardPayment;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class KorapayApiLogsCardTransactionRepository extends BaseRepository implements ApiLogsTransactionRepositoryInterface
{
    use UtilitiesTrait;

    public function create(int $transactionId): Model
    {
        return ApiLogsKoraCardPayment::create([
            'transaction_id' => $transactionId,
        ]);
    }

    public function update(int $transactionId, array $data): ?Model
    {
        $transaction = $this->findByColumn(ApiLogsKoraCardPayment::class, 'transaction_id', $transactionId);

        if (! $transaction) {
            return null;
        }

        $transaction->update($data);

        return $transaction;
    }
}
