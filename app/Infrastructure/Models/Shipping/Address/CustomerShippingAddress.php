<?php

namespace App\Infrastructure\Models\Shipping\Address;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\User\User;
use Database\Factories\Shipping\Address\CustomerShippingAddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $string, mixed $countryUUID)
 */
class CustomerShippingAddress extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id'];

    protected static function newFactory(): CustomerShippingAddressFactory
    {
        return CustomerShippingAddressFactory::new();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
