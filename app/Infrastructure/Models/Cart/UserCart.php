<?php

namespace App\Infrastructure\Models\Cart;

use App\Infrastructure\Models\User\User;
use Database\Factories\Cart\UserCartFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $id
 */
class UserCart extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function newFactory(): UserCartFactory
    {
        return UserCartFactory::new();
    }

    public function items(): HasMany
    {
        return $this->hasMany(UserCartItem::class, 'cart_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
