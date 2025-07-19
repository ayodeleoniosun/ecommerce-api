<?php

namespace App\Infrastructure\Models\Inventory;

use Database\Factories\Inventory\CategoryVariationOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static updateOrCreate(array $array, array $toArray)
 * @method static where(string $string, string $email)
 */
class CategoryVariationOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'uuid'];

    protected static function newFactory(): CategoryVariationOptionFactory
    {
        return CategoryVariationOptionFactory::new();
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(CategoryVariation::class, 'variation_id', 'id');
    }
}
