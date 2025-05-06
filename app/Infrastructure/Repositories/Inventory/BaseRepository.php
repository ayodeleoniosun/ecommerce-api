<?php

namespace App\Infrastructure\Repositories\Inventory;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    public function findByColumn(string $model, string $field, string $value): ?Model
    {
        return $model::where($field, $value)->first();
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }
}
