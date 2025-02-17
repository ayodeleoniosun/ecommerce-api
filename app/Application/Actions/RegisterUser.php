<?php

namespace App\Application\Actions;

use App\Domain\User\Entities\User\User as UserEntity;
use App\Domain\User\Events\UserRegisteredEvent;
use App\Domain\User\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;

class RegisterUser
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(UserEntity $user): User
    {
        $data = $this->userRepository->create($user);

        UserRegisteredEvent::dispatch($data);

        return $data;
    }
}
