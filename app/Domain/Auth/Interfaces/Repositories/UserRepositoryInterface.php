<?php

namespace App\Domain\Auth\Interfaces\Repositories;

use App\Domain\Auth\Entities\UserEntity;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;

interface UserRepositoryInterface
{
    public function create(UserEntity $user): User;

    public function findByEmail(string $email): ?User;

    public function verify(UserVerification $verification): User;

    public function resetPassword(array $request): string;
}
