<?php

namespace App\Domain\User\Interfaces\Repositories;

use App\Domain\User\Entities\User\User;
use App\Infrastructure\Models\UserVerification;

interface UserRepositoryInterface
{
    public function create(User $user);

    public function getToken(string $token);

    public function verify(UserVerification $verification);
}
