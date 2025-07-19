<?php

namespace App\Infrastructure\Models\Order;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Cart\UserCartItem;
use Database\Factories\Order\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static updateOrCreate(array $array, array $data)
 * @method static where(string $string, int $orderId)
 */
class OrderItem extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function newFactory(): OrderItemFactory
    {
        return OrderItemFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cartItem(): BelongsTo
    {
        return $this->belongsTo(UserCartItem::class);
    }
}
