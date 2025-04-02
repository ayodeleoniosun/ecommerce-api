<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Actions\Auth\ResetPassword;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->user = User::factory()->create();
});

it('should throw an exception if email is not found', function () {
    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn(null);

    $resetPassword = new ResetPassword($this->userRepo);
    $resetPassword->execute($this->user->email);
})->throws(ResourceNotFoundException::class, 'Email not found');

it('should throw an exception if user is not yet verified', function () {
    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $resetPassword = new ResetPassword($this->userRepo);
    $resetPassword->execute($this->user->email);
})->throws(BadRequestException::class, 'User not yet verified');

it('should throw an exception if user is not active', function () {
    $this->user->email_verified_at = now();

    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $resetPassword = new ResetPassword($this->userRepo);
    $resetPassword->execute($this->user->email);
})->throws(BadRequestException::class, 'User not active');

// it('can reset password', function () {
//    Event::fake();
//
//    $this->user->email_verified_at = now();
//    $this->user->status = UserEnum::ACTIVE->value;
//
//    $this->userRepo->shouldReceive('findByEmail')
//        ->once()
//        ->with($this->user->email)
//        ->andReturn($this->user);
//
//    $resetPassword = new ResetPassword($this->userRepo);
//    $status = $resetPassword->execute($this->user->email);
//
//    dd($status);
//
//    expect($status)->toBe(Password::RESET_LINK_SENT);
// });
