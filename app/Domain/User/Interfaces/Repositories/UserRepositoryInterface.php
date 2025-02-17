<?php

namespace App\Domain\User\Interfaces\Repositories;

use App\Domain\User\Entities\User\User;

interface UserRepositoryInterface
{
    public function create(User $user);
}
