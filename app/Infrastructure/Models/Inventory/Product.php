<?php

namespace App\Infrastructure\Models\Inventory;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Order\Order;
use App\Infrastructure\Models\Order\UserCart;
use App\Infrastructure\Models\Order\Wishlist;
use App\Infrastructure\Models\User\User;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static updateOrCreate(array $array, array $toArray)
 * @method static where(string $string, mixed $productUUID)
 */
class Product extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
        });

        static::deleting(function ($model) {
            $model->items()->delete();
        });
    }

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class, 'product_id', 'id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function firstItem(): HasOne
    {
        return $this->hasOne(ProductItem::class)->latest();
    }

    public function carts(): HasMany
    {
        return $this->hasMany(UserCart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}
