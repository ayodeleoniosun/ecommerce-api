<?php

namespace App\Infrastructure\Models\Payment\Integration\Flutterwave;

use App\Application\Shared\Traits\UtilitiesTrait;
use Database\Factories\Payment\Integration\Flutterwave\ApiLogFlutterwaveCardPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLogsFlutterwaveCardPayment extends Model
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

    protected static function newFactory(): ApiLogFlutterwaveCardPaymentFactory
    {
        return ApiLogFlutterwaveCardPaymentFactory::new();
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(TransactionFlutterwaveCardPayment::class);
    }
}
