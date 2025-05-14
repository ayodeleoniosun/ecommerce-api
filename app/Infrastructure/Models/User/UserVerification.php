<?php

namespace App\Infrastructure\Models\User;

use Database\Factories\UserVerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @method static create(array $data)
 *
 * @property Carbon|mixed $verified_at
 * @property mixed $user
 */
class UserVerification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $hidden = [
        'id', 'user_id',
    ];

    protected static function newFactory(): UserVerificationFactory
    {
        return UserVerificationFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
