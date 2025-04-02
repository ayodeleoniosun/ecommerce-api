<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Actions\Auth\ResendToken;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;
use Carbon\Carbon;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->userVerificationRepo = Mockery::mock(UserVerificationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->verification = UserVerification::factory()->create([
        'user_id' => $this->user->id,
    ]);
});
//
// it('should throw an exception if email is not found', function () {
//    $this->userRepo->shouldReceive('findByEmail')
//        ->once()
//        ->with($this->user->email)
//        ->andReturn(null);
//
//    $resendToken = new ResendToken($this->userRepo, $this->userVerificationRepo);
//    $resendToken->execute($this->user->email);
// })->throws(ResourceNotFoundException::class, 'Email not found');
//
// it('should throw an exception if user is already verified', function () {
//    $this->user->email_verified_at = now();
//
//    $this->userRepo->shouldReceive('findByEmail')
//        ->once()
//        ->with($this->user->email)
//        ->andReturn($this->user);
//
//    $resendToken = new ResendToken($this->userRepo, $this->userVerificationRepo);
//    $resendToken->execute($this->user->email);
// })->throws(BadRequestException::class, 'User already verified');

it('can resend token', function () {
    $mockedVerification = [
        'user_id' => $this->user->id,
        'token' => '12345',
        'expires_at' => Carbon::now()->addHours(6),
    ];

    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $this->userVerificationRepo->shouldReceive('create')
        ->once()
        ->with($mockedVerification)
        ->andReturn($this->verification);

    $resendToken = new ResendToken($this->userRepo, $this->userVerificationRepo);
    $resendToken->execute($this->user->email);
});
