<?php

namespace App\Infrastructure\Repositories;

use App\Domain\User\Entities\User\User as UserEntity;
use App\Domain\User\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;

class UserRepository implements UserRepositoryInterface
{

    public function create(UserEntity $user): User
    {
        return User::create([
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);
    }
}
