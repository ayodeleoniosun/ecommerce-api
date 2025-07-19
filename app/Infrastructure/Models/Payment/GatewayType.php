<?php

namespace App\Infrastructure\Models\Payment;

use App\Application\Shared\Traits\UtilitiesTrait;
use Database\Factories\Payment\GatewayTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatewayType extends Model
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

    protected static function newFactory(): GatewayTypeFactory
    {
        return GatewayTypeFactory::new();
    }

    public function primaryGateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class, 'primary_gateway_id');
    }

    public function secondaryGateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class, 'secondary_gateway_id');
    }
}
