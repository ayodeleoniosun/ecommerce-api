<?php

namespace App\Domain\Auth\Actions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Enums\UserStatusEnum;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;

class ResetPasswordAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(array $request): string
    {
        $user = $this->userRepository->findByColumn(User::class, 'email', $request['email']);

        throw_if(! $user, ResourceNotFoundException::class, 'Email not found');

        throw_if(! $user->email_verified_at, BadRequestException::class, 'User not yet verified');

        throw_if($user->status !== UserStatusEnum::ACTIVE->value, BadRequestException::class, 'User not active');

        $status = $this->userRepository->resetPassword($request);

        if ($status === Password::PASSWORD_RESET) {
            event(new PasswordReset($user));
        }

        return $status;
    }
}
