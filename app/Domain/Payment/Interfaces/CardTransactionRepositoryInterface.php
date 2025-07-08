<?php

namespace App\Domain\Payment\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface CardTransactionRepositoryInterface
{
    public function create(string $model, array $data): Model;

    public function update(string $model, array $data): ?Model;
}
