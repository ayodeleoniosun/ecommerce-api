<?php

namespace App\Application\Actions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\User\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\User\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\User;

class VerifyToken
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserVerificationRepositoryInterface $userVerificationRepository,
    ) {
    }

    public function execute(string $token): User
    {
        $verification = $this->userVerificationRepository->findByToken($token);

        throw_if(!$verification, ResourceNotFoundException::class, 'Token not found');

        throw_if($verification->expires_at < now(), BadRequestException::class, 'Token already expired');

        throw_if($verification->verified_at, BadRequestException::class, 'Account already verified');

        return $this->userRepository->verify($verification);
    }
}
