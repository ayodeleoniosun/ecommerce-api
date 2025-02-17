<?php

namespace App\Domain\User\Interfaces\Repositories;

use App\Infrastructure\Models\UserVerification;

interface UserVerificationRepositoryInterface
{
    public function create(array $data): UserVerification;

    public function findByToken(string $token): ?UserVerification;
}
