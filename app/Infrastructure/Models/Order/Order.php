<?php

namespace App\Infrastructure\Models\Order;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Cart\UserCart;
use App\Infrastructure\Models\User\User;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static create(array $array)
 * @method static firstOrCreate(array $array, array $array1)
 */
class Order extends Model
{
    use HasFactory, UtilitiesTrait;

    protected $guarded = ['id'];

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
            $model->reference = self::generateRandomCharacters('ORDER-');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(UserCart::class);
    }

    public function shipping(): HasOne
    {
        return $this->hasOne(OrderShipping::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(OrderPayment::class)->latest();
    }
}
