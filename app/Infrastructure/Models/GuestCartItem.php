<?php

namespace App\Infrastructure\Models;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static updateOrCreate(array $array, array $toCartArray)
 */
class GuestCartItem extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id'];

    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(GuestCart::class);
    }
}
