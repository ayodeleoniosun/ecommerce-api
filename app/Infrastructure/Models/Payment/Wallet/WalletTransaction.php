<?php

namespace App\Infrastructure\Models\Payment\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use Database\Factories\Payment\Wallet\WalletTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTransaction extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function boot()
    {
        parent::boot();
    }

    protected static function newFactory(): WalletTransactionFactory
    {
        return WalletTransactionFactory::new();
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
