<?php

namespace App\Infrastructure\Models\User;

use Database\Factories\User\PasswordResetTokenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected static function newFactory(): PasswordResetTokenFactory
    {
        return PasswordResetTokenFactory::new();
    }
}
