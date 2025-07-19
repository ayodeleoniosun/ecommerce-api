<?php

namespace App\Infrastructure\Models\Cart;

use App\Infrastructure\Models\Inventory\ProductItem;
use Database\Factories\Cart\UserCartItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static updateOrCreate(array $array, array $toCartItemArray)
 *
 * @property mixed $quantity
 */
class UserCartItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function newFactory(): UserCartItemFactory
    {
        return UserCartItemFactory::new();
    }

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(UserCart::class);
    }
}
