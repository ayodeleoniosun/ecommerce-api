<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Actions\ResetPassword;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Requests\ResetPasswordRequest;
use App\Infrastructure\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->resetPassword = new ResetPassword($this->userRepo);
    $this->request = new ResetPasswordRequest;
    $this->request->merge([
        'email' => $this->user->email,
        'token' => 'this_token',
        'password' => 'new_password',
        'password_confirmation' => 'new_password',
    ]);
});

it('should throw an exception if email is not found', function () {
    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn(null);

    $this->resetPassword->execute($this->request->all());
})->throws(ResourceNotFoundException::class, 'Email not found');

it('should throw an exception if user is not yet verified', function () {
    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn($this->user);

    $this->resetPassword->execute($this->request->all());
})->throws(BadRequestException::class, 'User not yet verified');

it('should throw an exception if user is not active', function () {
    $this->user->email_verified_at = now();

    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn($this->user);

    $this->resetPassword->execute($this->request->all());
})->throws(BadRequestException::class, 'User not active');

it('can reset password', function () {
    Event::fake();

    $this->user->email_verified_at = now();
    $this->user->status = UserEnum::ACTIVE->value;

    $this->userRepo->shouldReceive('findByColumn')
        ->once()
        ->with('email', $this->user->email)
        ->andReturn($this->user);

    $this->userRepo->shouldReceive('resetPassword')
        ->once()
        ->with($this->request->all())
        ->andReturn(Password::PASSWORD_RESET);

    $status = $this->resetPassword->execute($this->request->all());

    expect($status)->toBe(Password::PASSWORD_RESET);

    Event::assertDispatched(PasswordReset::class, function ($event) {
        return $event->user->id === $this->user->id;
    });

});
