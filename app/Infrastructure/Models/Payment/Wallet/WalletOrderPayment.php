<?php

namespace App\Infrastructure\Models\Payment\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Order\OrderPayment;
use Database\Factories\Payment\Wallet\WalletOrderPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletOrderPayment extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function boot()
    {
        parent::boot();
    }

    protected static function newFactory(): WalletOrderPaymentFactory
    {
        return WalletOrderPaymentFactory::new();
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function orderPayment(): BelongsTo
    {
        return $this->belongsTo(OrderPayment::class);
    }
}
