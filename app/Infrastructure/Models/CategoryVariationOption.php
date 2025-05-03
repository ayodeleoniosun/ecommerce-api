<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static updateOrCreate(array $array, array $toArray)
 * @method static where(string $string, string $email)
 */
class CategoryVariationOption extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'uuid'];

    public function categoryVariation(): BelongsTo
    {
        return $this->belongsTo(CategoryVariation::class);
    }
}
