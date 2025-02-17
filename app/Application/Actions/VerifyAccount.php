<?php

namespace App\Application\Actions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\User\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;

class VerifyAccount
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(string $token): User
    {
        $verification = $this->userRepository->getToken($token);

        throw_if(!$verification, BadRequestException::class, 'Invalid token');

        throw_if($verification->expires_at < now(), BadRequestException::class, 'Token already expired');

        throw_if($verification->verified_at, BadRequestException::class, 'Account already verified');

        return $this->userRepository->verify($verification);
    }
}
