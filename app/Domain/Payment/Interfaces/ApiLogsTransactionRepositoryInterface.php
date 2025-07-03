<?php

namespace App\Domain\Payment\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ApiLogsTransactionRepositoryInterface
{
    public function create(int $transactionId): Model;

    public function update(int $transactionId, array $data): ?Model;
}
