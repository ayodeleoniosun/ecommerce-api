<?php

namespace App\Infrastructure\Models\Payment\Integration\Flutterwave;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionFlutterwaveCardPayment extends Model
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
        return $this->hasOne(ApiLogsFlutterwaveCardPayment::class, 'transaction_id');
    }
}
