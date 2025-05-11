<?php

namespace App\Infrastructure\Models;

use App\Application\Shared\Traits\UtilitiesTrait;
use Database\Factories\ProductItemFactory;
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
class ProductItem extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    public static function getPriceRange(int $productId): array|string|null
    {
        $range = self::selectRaw('MIN(price) as min, MAX(price) as max')->where('product_id', $productId)
            ->first();

        if ($range->min === $range->max) {
            return $range->min;
        }

        return [
            'min' => $range->min,
            'max' => $range->max,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
            $model->sku = 'PRO-SKU-'.self::generateRandomCharacters();
        });

        static::deleting(function ($model) {
            $model->images()->delete();
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_item_id', 'id');
    }

    protected static function newFactory(): ProductItemFactory
    {
        return ProductItemFactory::new();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function variationOption(): BelongsTo
    {
        return $this->belongsTo(CategoryVariationOption::class);
    }

    public function firstImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_item_id', 'id')->latest();
    }
}
