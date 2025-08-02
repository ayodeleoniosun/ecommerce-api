<?php

namespace App\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    public function findByColumn(string $model, string $field, string $value): ?Model
    {
        return $model::where($field, $value)->first();
    }

    public function deleteByColumn(string $model, string $field, string $value): int
    {
        return $model::where($field, $value)->delete();
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

    public function lockForUpdate(Model $model): Model
    {
        return $model->lockForUpdate()->first();
    }
}
