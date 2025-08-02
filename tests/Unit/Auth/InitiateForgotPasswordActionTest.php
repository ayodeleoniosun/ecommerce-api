<?php

namespace Tests\Unit\Auth;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Actions\InitiateForgotPasswordAction;
use App\Domain\Auth\Enums\UserStatusEnum;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepository::class)->makePartial();
    $this->user = User::factory()->create();
    $this->forgotPassword = new InitiateForgotPasswordAction($this->userRepo);
});

describe('Forgot Password', function () {
    it('should throw an exception if email is not found', function () {
        $this->forgotPassword->execute('invalid_email');
    })->throws(ResourceNotFoundException::class, 'Email not found');

    it('should throw an exception if user is not yet verified', function () {
        $this->forgotPassword->execute($this->user->email);
    })->throws(BadRequestException::class, 'User not yet verified');

    it('should throw an exception if user is not active', function () {
        $this->user->email_verified_at = now();
        $this->user->save();

        $this->forgotPassword->execute($this->user->email);
    })->throws(BadRequestException::class, 'User not active');

    it('can send a forgot password link', function () {
        Event::fake();

        $this->user->email_verified_at = now();
        $this->user->status = UserStatusEnum::ACTIVE->value;
        $this->user->save();

        $status = $this->forgotPassword->execute($this->user->email);

        expect($status)->toBe(Password::RESET_LINK_SENT);
    });
});
