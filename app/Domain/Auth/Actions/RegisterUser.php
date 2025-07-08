<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Dtos\CreateUserDto;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Notifications\RegistrationCompletedNotification;
use App\Infrastructure\Models\User\User;

class RegisterUser
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function execute(CreateUserDto $userDto): User
    {
        $user = $this->userRepository->create($userDto);

        $user->notify(new RegistrationCompletedNotification($user));

        return $user;
    }
}
