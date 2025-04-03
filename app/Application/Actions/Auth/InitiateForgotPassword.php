<?php

namespace App\Application\Actions\Auth;

use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use Illuminate\Support\Facades\Password;

class InitiateForgotPassword
{
    public function __construct(
        private readonly \App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface $userRepository,
    ) {}

    public function execute(string $email): string
    {
        $user = $this->userRepository->findByEmail($email);

        throw_if(! $user, ResourceNotFoundException::class, 'Email not found');

        throw_if(! $user->email_verified_at, BadRequestException::class, 'User not yet verified');

        throw_if($user->status !== UserEnum::ACTIVE->value, BadRequestException::class, 'User not active');

        return Password::sendResetLink(compact('email'));
    }
}
