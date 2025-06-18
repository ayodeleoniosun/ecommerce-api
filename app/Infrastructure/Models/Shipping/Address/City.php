<?php

namespace App\Infrastructure\Models\Shipping\Address;

use App\Application\Shared\Traits\UtilitiesTrait;
use App\Infrastructure\Models\Shipping\PickupStation\PickupStation;
use Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(string $string, mixed $cityUUID)
 */
class City extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id', 'uuid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = self::generateUUID();
        });
    }

    protected static function newFactory(): CityFactory
    {
        return CityFactory::new();
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function pickupStations()
    {
        return $this->hasMany(PickupStation::class);
    }
}
