<?php

namespace App\Infrastructure\Models\Payment\Integration;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionKoraCardPayment extends Model
{
    use HasFactory, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
        });
    }

    public function apiLog(): HasOne
    {
        return $this->hasOne(ApiLogsKoraCardPayment::class, 'transaction_id');
    }
}
