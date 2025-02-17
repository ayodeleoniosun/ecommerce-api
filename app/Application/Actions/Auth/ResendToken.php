<?php

namespace App\Application\Actions\Auth;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Events\VerificationMailResentEvent;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ResendToken
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserVerificationRepositoryInterface $userVerificationRepository,
    ) {}

    public function execute(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        throw_if(! $user, ResourceNotFoundException::class, 'Email not found');

        throw_if($user->email_verified_at, BadRequestException::class, 'User already verified');

        $verification = $this->userVerificationRepository->create([
            'user_id' => $user->id,
            'token' => hash('sha256', Str::random(40)),
            'expires_at' => Carbon::now()->addHours(6),
        ]);

        $verification->user = $user;

        VerificationMailResentEvent::dispatch($verification);
    }
}
