<?php

namespace Tests\Unit\Auth;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Actions\ResetPasswordAction;
use App\Domain\Auth\Enums\UserStatusEnum;
use App\Domain\Auth\Requests\ResetPasswordRequest;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Repositories\User\UserRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepository::class)->makePartial();
    $this->user = User::factory()->create();
    $this->resetPassword = new ResetPasswordAction($this->userRepo);
    $this->request = new ResetPasswordRequest;
    $this->request->merge([
        'email' => $this->user->email,
        'token' => 'this_token',
        'password' => 'new_password',
        'password_confirmation' => 'new_password',
    ]);
});

describe('Reset Password', function () {
    it('should throw an exception if email is not found', function () {
        $payload = $this->request->all();
        $payload['email'] = 'invalid_email';

        $this->resetPassword->execute($payload);
    })->throws(ResourceNotFoundException::class, 'Email not found');

    it('should throw an exception if user is not yet verified', function () {
        $this->resetPassword->execute($this->request->all());
    })->throws(BadRequestException::class, 'User not yet verified');

    it('should throw an exception if user is not active', function () {
        $this->user->email_verified_at = now();
        $this->user->save();

        $this->resetPassword->execute($this->request->all());
    })->throws(BadRequestException::class, 'User not active');

    it('can reset password', function () {
        Event::fake();
        Mail::fake();

        $this->user->email_verified_at = now();
        $this->user->status = UserStatusEnum::ACTIVE->value;
        $this->user->save();

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
});
