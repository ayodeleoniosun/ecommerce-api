<?php

namespace App\Infrastructure\Models\Payment\Wallet;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\User\User;
use Database\Factories\Shipping\Address\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function boot()
    {
        parent::boot();
    }

    protected static function newFactory(): CityFactory
    {
        return CityFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
