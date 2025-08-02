<?php

namespace Tests\Unit\Auth;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Actions\LoginUserAction;
use App\Domain\Auth\Enums\UserStatusEnum;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\User\UserRepository;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepository::class)->makePartial();
    $this->user = User::factory()->create();
    $this->payload = [
        'email' => $this->user->email,
        'password' => 'Ayodele@2025',
    ];
    $this->loginUser = new LoginUserAction($this->userRepo);
});

describe('Login', function () {
    it('should throw an exception if user is not found', function () {
        $payload = $this->payload;
        $payload['email'] = 'invalid_email';

        $this->loginUser->execute($payload);
    })->throws(ResourceNotFoundException::class, 'User not found');

    it('should throw an exception if email is not yet verified', function () {
        $this->loginUser->execute($this->payload);
    })->throws(BadRequestException::class, 'Email not yet verified');

    it('should throw an exception if account is inactive', function () {
        $this->user->email_verified_at = now();
        $this->user->save();

        $this->loginUser->execute($this->payload);
    })->throws(BadRequestException::class, 'Account is inactive');

    it('should throw an exception if password does not match', function () {
        $this->user->email_verified_at = now();
        $this->user->status = UserStatusEnum::ACTIVE->value;
        $this->user->save();

        $this->payload['password'] = 'wrong_password';

        $this->loginUser->execute($this->payload);
    })->throws(BadRequestException::class, 'Invalid login credentials');

    it('can login successfully', function () {
        $this->user->email_verified_at = now();
        $this->user->status = UserStatusEnum::ACTIVE->value;
        $this->user->save();

        $response = $this->loginUser->execute($this->payload);

        expect($response)->toBeInstanceOf(User::class)
            ->and($response->firstname)->toBe($this->user->firstname)
            ->and($response->lastname)->toBe($this->user->lastname)
            ->and($response->email)->toBe($this->user->email);
    });
});
