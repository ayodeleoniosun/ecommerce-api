<?php

namespace App\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    public function findByColumn(string $model, string $field, string $value): ?Model
    {
        return $model::where($field, $value)->first();
    }

    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }

    public function findAllByColumn(string $model, string $field, string $value)
    {
        return $model::where($field, $value);
    }

    public function updateColumns(Model $model, array $data): bool
    {
        return $model->update($data);
    }
}
