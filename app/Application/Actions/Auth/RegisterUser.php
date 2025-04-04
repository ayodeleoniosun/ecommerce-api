<?php

namespace App\Application\Actions\Auth;

use App\Domain\Auth\Dtos\UserDto;
use App\Domain\Auth\Events\UserRegisteredEvent;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;

class RegisterUser
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function execute(UserDto $userDto): User
    {
        $user = $this->userRepository->create($userDto);

        UserRegisteredEvent::dispatch($user);

        return $user;
    }
}
