<?php

namespace App\Infrastructure\Repositories\Payment;

use App\Domain\Payment\Interfaces\CardTransactionRepositoryInterface;
use App\Infrastructure\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class CardTransactionRepository extends BaseRepository implements CardTransactionRepositoryInterface
{
    public function create(string $model, array $data): Model
    {
        return $model::create($data);
    }

    public function update(string $model, array $data): ?Model
    {
        $transaction = $this->findByColumn($model, 'id', $data['transaction_id']);

        if (! $transaction) {
            return null;
        }

        $transaction->update($data);

        return $transaction;
    }
}
