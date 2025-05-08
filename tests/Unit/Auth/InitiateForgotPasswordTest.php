<?php

namespace Tests\Unit\Auth;

use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Actions\InitiateForgotPassword;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->forgotPassword = new InitiateForgotPassword($this->userRepo);
});

it('should throw an exception if email is not found', function () {
    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn(null);

    $this->forgotPassword->execute($this->user->email);
})->throws(ResourceNotFoundException::class, 'Email not found');

it('should throw an exception if user is not yet verified', function () {
    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn($this->user);

    $this->forgotPassword->execute($this->user->email);
})->throws(BadRequestException::class, 'User not yet verified');

it('should throw an exception if user is not active', function () {
    $this->user->email_verified_at = now();

    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn($this->user);

    $this->forgotPassword->execute($this->user->email);
})->throws(BadRequestException::class, 'User not active');

it('can send a forgot password link', function () {
    Event::fake();

    $this->user->email_verified_at = now();
    $this->user->status = UserEnum::ACTIVE->value;

    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn($this->user);

    $status = $this->forgotPassword->execute($this->user->email);

    expect($status)->toBe(Password::RESET_LINK_SENT);
});
