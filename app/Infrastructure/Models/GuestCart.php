<?php

namespace App\Infrastructure\Models;

use App\Application\Shared\Traits\UtilitiesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestCart extends Model
{
    use HasFactory, SoftDeletes, UtilitiesTrait;

    protected $guarded = ['id'];

    public function cartItems(): HasMany
    {
        return $this->hasMany(GuestCartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
