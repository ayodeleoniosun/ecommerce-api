<?php

namespace App\Domain\Auth\Actions;

use App\Application\Shared\Enum\UserStatusEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Password;

class InitiateForgotPasswordAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(string $email): string
    {
        $user = $this->userRepository->findByColumn('email', $email);

        throw_if(! $user, ResourceNotFoundException::class, 'Email not found');

        throw_if(! $user->email_verified_at, BadRequestException::class, 'User not yet verified');

        throw_if($user->status !== UserStatusEnum::ACTIVE->value, BadRequestException::class, 'User not active');

        return Password::sendResetLink(compact('email'));
    }
}
