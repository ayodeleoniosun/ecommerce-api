<?php

namespace App\Infrastructure\Repositories;

use App\Domain\User\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\UserVerification;

class UserVerificationRepository implements UserVerificationRepositoryInterface
{
    public function findByToken(string $token): ?UserVerification
    {
        return UserVerification::with('user')
            ->where('token', $token)
            ->first();
    }

    public function create(array $data): UserVerification
    {
        return UserVerification::create($data);
    }
}
