<?php

namespace App\Infrastructure\Models\Inventory;

use App\Application\Shared\Traits\UtilitiesTrait;
use Database\Factories\Inventory\CategoryVariationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static updateOrCreate(array $array, array $toArray)
 * @method static where(string $string, string $email)
 */
class CategoryVariation extends Model
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
            $model->options()->delete();
        });
    }

    protected static function newFactory(): CategoryVariationFactory
    {
        return CategoryVariationFactory::new();
    }

    public function options(): HasMany
    {
        return $this->hasMany(CategoryVariationOption::class, 'variation_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
