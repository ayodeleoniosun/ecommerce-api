<?php

namespace App\Infrastructure\Models;

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

    public function variation(): BelongsTo
    {
        return $this->belongsTo(CategoryVariation::class, 'variation_id', 'id');
    }
}
