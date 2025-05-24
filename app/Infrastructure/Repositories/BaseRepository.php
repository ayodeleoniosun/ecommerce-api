<?php

namespace App\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    public function findAllByColumn(string $model, string $field, string $value): Collection
    {
        return $model::where($field, $value)->get();
    }
}
