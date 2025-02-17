<?php

namespace App\Domain\Auth\Interfaces\Repositories;

use App\Domain\Auth\Entities\User as UserEntity;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;

interface UserRepositoryInterface
{
    public function create(UserEntity $user): array;

    public function findByEmail(string $email): ?User;

    public function verify(UserVerification $verification): User;
}
