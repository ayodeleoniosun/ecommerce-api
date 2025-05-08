<?php

namespace Tests\Unit\Auth;

use App\Application\Events\Auth\VerificationMailResentEvent;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Actions\ResendToken;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;
use Illuminate\Support\Facades\Event;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->userVerificationRepo = Mockery::mock(UserVerificationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->verification = UserVerification::factory()->create([
        'user_id' => $this->user->id,
    ]);
    $this->resendToken = new ResendToken($this->userRepo, $this->userVerificationRepo);
});

it('should throw an exception if email is not found', function () {
    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn(null);

    $this->resendToken->execute($this->user->email);
})->throws(ResourceNotFoundException::class, 'Email not found');

it('should throw an exception if user is already verified', function () {
    $this->user->email_verified_at = now();

    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn($this->user);

    $this->resendToken->execute($this->user->email);
})->throws(BadRequestException::class, 'User already verified');

it('can resend token', function () {
    Event::fake();

    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn($this->user);

    $this->userVerificationRepo->shouldReceive('create')
        ->once()
        ->andReturn($this->verification);

    $this->resendToken->execute($this->user->email);

    Event::assertDispatched(VerificationMailResentEvent::class, function ($event) {
        return $event->verification->user->id === $this->user->id;
    });
});
