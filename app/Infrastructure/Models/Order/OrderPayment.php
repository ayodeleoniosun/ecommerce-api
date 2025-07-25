<?php

namespace App\Infrastructure\Models\Order;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Payment\Wallet\WalletOrderPayment;
use Database\Factories\Order\OrderPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function newFactory(): OrderPaymentFactory
    {
        return OrderPaymentFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
            $model->reference = self::generateRandomCharacters('PAY-');
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function walletPayments(): HasMany
    {
        return $this->hasMany(WalletOrderPayment::class, 'order_payment_id');
    }
}
