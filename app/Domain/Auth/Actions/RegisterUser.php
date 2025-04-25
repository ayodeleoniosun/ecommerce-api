<?php

namespace App\Domain\Auth\Actions;

use App\Application\Events\Auth\UserRegisteredEvent;
use App\Domain\Auth\Dtos\CreateUserDto;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;

class RegisterUser
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function execute(CreateUserDto $userDto): User
    {
        $user = $this->userRepository->create($userDto);

        UserRegisteredEvent::dispatch($user);

        return $user;
    }
}
