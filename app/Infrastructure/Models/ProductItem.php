<?php

namespace App\Infrastructure\Models;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static updateOrCreate(array $array, array $toArray)
 * @method static where(string $string, mixed $productUUID)
 */
class ProductItem extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
            $model->sku = 'PRO-SKU-'.self::generateRandomCharacters();
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variationOption(): BelongsTo
    {
        return $this->belongsTo(CategoryVariationOption::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_item_id', 'id');
    }
}
