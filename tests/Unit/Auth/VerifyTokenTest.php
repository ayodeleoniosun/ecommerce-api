<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Actions\Auth\VerifyToken;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->userVerificationRepo = Mockery::mock(UserVerificationRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->verification = UserVerification::factory()->create([
        'user_id' => $this->user->id,
    ]);
    $this->token = '12345';

    $this->verifyToken = new VerifyToken($this->userRepo, $this->userVerificationRepo);
});

it('should throw an exception if token is not found', function () {
    $this->userVerificationRepo->shouldReceive('findByToken')
        ->once()
        ->with($this->token)
        ->andReturn(null);

    $this->verifyToken->execute($this->token);
})->throws(ResourceNotFoundException::class, 'Token not found');

it('should throw an exception if token already expired', function () {
    $this->verification->expires_at = now()->subHour();

    $this->userVerificationRepo->shouldReceive('findByToken')
        ->once()
        ->with($this->token)
        ->andReturn($this->verification);

    $this->verifyToken->execute($this->token);
})->throws(BadRequestException::class, 'Token already expired');

it('should throw an exception if token already verified', function () {
    $this->verification->expires_at = now()->addHour();
    $this->verification->verified_at = now();

    $this->userVerificationRepo->shouldReceive('findByToken')
        ->once()
        ->with($this->token)
        ->andReturn($this->verification);

    $this->verifyToken->execute($this->token);
})->throws(BadRequestException::class, 'Account already verified');

it('can verify valid token', function () {
    $this->verification->expires_at = now()->addHour();

    $this->userVerificationRepo->shouldReceive('findByToken')
        ->once()
        ->with($this->token)
        ->andReturn($this->verification);

    $this->userRepo->shouldReceive('verify')
        ->once()
        ->with($this->verification)
        ->andReturn($this->user);

    $response = $this->verifyToken->execute($this->token);

    expect($response)->toBeInstanceOf(User::class)
        ->and($response->firstname)->toBe($this->user->firstname)
        ->and($response->lastname)->toBe($this->user->lastname)
        ->and($response->email)->toBe($this->user->email);
});
