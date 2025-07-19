<?php

namespace App\Infrastructure\Models\Inventory;

use Database\Factories\Inventory\ProductImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function newFactory(): ProductImageFactory
    {
        return ProductImageFactory::new();
    }

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id', 'id');
    }
}
