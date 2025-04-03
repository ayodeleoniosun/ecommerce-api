<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Actions\Auth\LoginUser;
use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Infrastructure\Models\User;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(\App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface::class);
    $this->user = User::factory()->create();
    $this->payload = [
        'email' => $this->user->email,
        'password' => 'Ayodele@2025',
    ];
    $this->loginUser = new LoginUser($this->userRepo);
});

it('should throw an exception if user is not found', function () {
    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn(null);

    $this->loginUser->execute($this->payload);
})->throws(ResourceNotFoundException::class, 'User not found');

it('should throw an exception if email is not yet verified', function () {
    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $this->loginUser->execute($this->payload);
})->throws(BadRequestException::class, 'Email not yet verified');

it('should throw an exception if account is inactive', function () {
    $this->user->email_verified_at = now();

    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $this->loginUser->execute($this->payload);
})->throws(BadRequestException::class, 'Account is inactive');

it('should throw an exception if password does not match', function () {
    $this->user->email_verified_at = now();
    $this->user->status = UserEnum::ACTIVE->value;
    $this->payload['password'] = 'wrong_password';

    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $this->loginUser->execute($this->payload);
})->throws(BadRequestException::class, 'Invalid login credentials');

it('can login successfully', function () {
    $this->user->email_verified_at = now();
    $this->user->status = UserEnum::ACTIVE->value;

    $this->userRepo->shouldReceive('findByEmail')
        ->once()
        ->with($this->user->email)
        ->andReturn($this->user);

    $response = $this->loginUser->execute($this->payload);

    expect($response)->toBeInstanceOf(User::class)
        ->and($response->firstname)->toBe($this->user->firstname)
        ->and($response->lastname)->toBe($this->user->lastname)
        ->and($response->email)->toBe($this->user->email);
});
