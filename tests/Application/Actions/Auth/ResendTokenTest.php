<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Actions\Auth\ResendToken;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Events\Auth\VerificationMailResentEvent;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserVerificationRepositoryInterface;
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
    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn(null);

    $this->resendToken->execute($this->user->email);
})->throws(ResourceNotFoundException::class, 'Email not found');

it('should throw an exception if user is already verified', function () {
    $this->user->email_verified_at = now();

    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $this->resendToken->execute($this->user->email);
})->throws(BadRequestException::class, 'User already verified');

it('can resend token', function () {
    Event::fake();

    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $this->userVerificationRepo->shouldReceive('create')
        ->once()
        ->andReturn($this->verification);

    $this->resendToken->execute($this->user->email);

    Event::assertDispatched(VerificationMailResentEvent::class, function ($event) {
        return $event->verification->user->id === $this->user->id;
    });
});
