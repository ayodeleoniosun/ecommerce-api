<?php

namespace App\Domain\Auth\Actions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Domain\Auth\Notifications\RegistrationCompletedNotification;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\User\UserVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResendTokenAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserVerificationRepositoryInterface $userVerificationRepository,
    ) {}

    public function execute(string $email): void
    {
        $user = $this->userRepository->findByColumn(User::class, 'email', $email);

        throw_if(! $user, ResourceNotFoundException::class, 'Email not found');

        throw_if($user->email_verified_at, BadRequestException::class, 'User already verified');

        $verification = null;

        DB::transaction(function () use ($user, &$verification) {
            $this->userVerificationRepository->deleteByColumn(
                UserVerification::class,
                'user_id',
                $user->id,
            );

            $verification = $this->userVerificationRepository->create([
                'user_id' => $user->id,
                'token' => hash('sha256', Str::random(40)),
                'expires_at' => Carbon::now()->addHours(6),
            ]);
        });

        $verification->user = $user;

        $user->notify(new RegistrationCompletedNotification($user));
    }
}
