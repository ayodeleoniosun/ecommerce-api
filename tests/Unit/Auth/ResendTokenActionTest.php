<?php

namespace Tests\Unit\Auth;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Actions\ResendTokenAction;
use App\Domain\Auth\Notifications\RegistrationCompletedNotification;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\User\UserVerification;
use App\Infrastructure\Repositories\User\UserRepository;
use App\Infrastructure\Repositories\User\UserVerificationRepository;
use Illuminate\Support\Facades\Notification;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepository::class)->makePartial();
    $this->userVerificationRepo = Mockery::mock(UserVerificationRepository::class)->makePartial();
    $this->user = User::factory()->create();
    $this->verification = UserVerification::factory()->create([
        'user_id' => $this->user->id,
    ]);
    $this->resendToken = new ResendTokenAction($this->userRepo, $this->userVerificationRepo);
});

describe('Resend Token', function () {
    it('should throw an exception if email is not found', function () {
        $this->resendToken->execute('invalid_email');
    })->throws(ResourceNotFoundException::class, 'Email not found');

    it('should throw an exception if user is already verified', function () {
        $this->user->email_verified_at = now();
        $this->user->save();

        $this->resendToken->execute($this->user->email);
    })->throws(BadRequestException::class, 'User already verified');

    it('can resend token', function () {
        Notification::fake();

        $this->userVerificationRepo->shouldReceive('deleteByColumn')
            ->once()
            ->andReturn(1);

        $this->userVerificationRepo->shouldReceive('create')
            ->once()
            ->andReturn($this->verification);

        $this->resendToken->execute($this->user->email);

        Notification::assertSentTo($this->user, RegistrationCompletedNotification::class);
    });
});
