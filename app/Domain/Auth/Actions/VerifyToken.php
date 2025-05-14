<?php

namespace App\Domain\Auth\Actions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\User\User;

class VerifyToken
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserVerificationRepositoryInterface $userVerificationRepository,
    ) {}

    public function execute(string $token): User
    {
        $verification = $this->userVerificationRepository->findByToken($token);

        throw_if(! $verification, ResourceNotFoundException::class, 'Token not found');

        throw_if($verification->expires_at < now(), BadRequestException::class, 'Token already expired');

        throw_if($verification->verified_at, BadRequestException::class, 'Account already verified');

        return $this->userRepository->verify($verification);
    }
}
