<?php

namespace App\Infrastructure\Models\Inventory;

use App\Application\Shared\Traits\UtilitiesTrait;
use Database\Factories\Inventory\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static updateOrCreate(array $array, array $toArray)
 * @method static where(string $string, string $email)
 */
class Category extends Model
{
    use HasFactory, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
        });
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(CategoryVariation::class);
    }
}
