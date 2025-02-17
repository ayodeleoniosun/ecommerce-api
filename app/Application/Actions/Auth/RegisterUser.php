<?php

namespace App\Application\Actions\Auth;

use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Events\UserRegisteredEvent;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
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

        return $data['user'];
    }
}
