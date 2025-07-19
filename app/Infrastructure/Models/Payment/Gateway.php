<?php

namespace App\Infrastructure\Models\Payment;

use App\Application\Shared\Traits\UtilitiesTrait;
use Database\Factories\Payment\GatewayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gateway extends Model
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

    protected static function newFactory(): GatewayFactory
    {
        return GatewayFactory::new();
    }

    public function primaryGatewayType(): HasMany
    {
        return $this->hasMany(GatewayType::class, 'primary_gateway_id');
    }

    public function secondaryGatewayType(): HasMany
    {
        return $this->hasMany(GatewayType::class, 'secondary_gateway_id');
    }

    public function configuration(): HasMany
    {
        return $this->hasMany(GatewayConfiguration::class);
    }
}
