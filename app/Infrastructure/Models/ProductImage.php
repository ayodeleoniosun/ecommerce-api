<?php

namespace App\Infrastructure\Models;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id'];

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id', 'id');
    }
}
