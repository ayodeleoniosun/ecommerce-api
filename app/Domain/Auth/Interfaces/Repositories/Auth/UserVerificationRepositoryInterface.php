<?php

namespace App\Domain\Auth\Interfaces\Repositories\Auth;

use App\Infrastructure\Models\UserVerification;

interface UserVerificationRepositoryInterface
{
    public function create(array $data): UserVerification;

    public function findByToken(string $token): ?UserVerification;
}
