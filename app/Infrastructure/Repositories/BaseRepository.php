<?php

namespace App\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    public function findByColumn(string $model, string $field, string $value): ?Model
    {
        return $model::where($field, $value)->first();
    }

    public function deleteByColumn(string $model, string $field, string $value): void
    {
        $model::where($field, $value)->delete();
    }

    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }
}
