<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Events\Auth\VerificationMailResentEvent;
use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Enum\UserTypeEnum;
use App\Infrastructure\Models\PasswordResetToken;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;

describe('user registration', function () {
    it('should return an error if a required field is empty', function () {
        $payload = [
            'firstname' => 'John',
        ];

        $response = $this->postJson('/api/auth/register', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The lastname field is required.');
    });

    it('should return an error if invalid email address is supplied', function () {
        $payload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone_number' => '08123456789',
            'email' => 'invalid_email',
            'password' => 'password123',
            'type' => UserTypeEnum::CUSTOMER->value,
        ];

        $response = $this->postJson('/api/auth/register', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The email field must be a valid email address.');
    });

    it('should return an error if password is weak', function () {
        $payload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone_number' => '08123456789',
            'email' => 'valid@email.com',
            'password' => 'password123',
            'type' => UserTypeEnum::CUSTOMER->value,
        ];

        $response = $this->postJson('/api/auth/register', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The password field must contain at least one uppercase and one lowercase letter.');
    });

    it('can register a new user', function () {
        $payload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone_number' => '08123456789',
            'email' => 'valid@email.com',
            'password' => 'Ayodele@2025',
            'type' => UserTypeEnum::CUSTOMER->value,
        ];

        $response = $this->postJson('/api/auth/register', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'firstname',
                    'lastname',
                    'email',
                    'type',
                ],
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('User registered successfully')
            ->and($content->data->firstname)->toBe($payload['firstname'])
            ->and($content->data->lastname)->toBe($payload['lastname'])
            ->and($content->data->email)->toBe($payload['email']);
    });

    it('should return an error if email address already exist', function () {
        $user = User::factory()->create();

        $payload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone_number' => '08123456789',
            'email' => $user->email,
            'password' => 'Ayodele@2025',
            'type' => UserTypeEnum::CUSTOMER->value,
        ];

        $response = $this->postJson('/api/auth/register', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The email has already been taken.');
    });
});

describe('user login', function () {
    it('should return an error if email address does not exist', function () {
        $payload = [
            'email' => 'non_existing@email.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Email address does not exist. Try registering a new account');
    });

    it('should return an error if user is unverified', function () {
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'Ayodele@2025',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Email not yet verified');
    });

    it('should return an error if user is inactive', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'Ayodele@2025',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Account is inactive');
    });

    it('should return an error if login credentials is invalid', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => UserEnum::ACTIVE->value,
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'InvalidPassword',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Invalid login credentials');
    });

    it('should login successfully', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => UserEnum::ACTIVE->value,
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'Ayodele@2025',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'firstname',
                    'lastname',
                    'email',
                    'status',
                    'email_verified_at',
                    'type',
                    'token',
                ],
            ]);

        expect($content->success)->toBe(true)
            ->and($content->data->firstname)->toBe($user->firstname)
            ->and($content->data->lastname)->toBe($user->lastname)
            ->and($content->data->email)->toBe($user->email)
            ->and($content->data->status)->toBe($user->status);
    });
});

describe('verify token', function () {
    it('should return an error if token does not exist', function () {
        $payload = [
            'token' => 'invalid_token',
        ];

        $response = $this->postJson('/api/auth/token/verify', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The selected token is invalid.');
    });

    it('should return an error if token has already expired', function () {
        $verification = UserVerification::factory()->create([
            'expires_at' => now()->subHour(),
        ]);

        $payload = [
            'token' => $verification->token,
        ];

        $response = $this->postJson('/api/auth/token/verify', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Token already expired');
    });

    it('should return an error if account is already verified', function () {
        $verification = UserVerification::factory()->create([
            'expires_at' => now(),
            'verified_at' => now(),
        ]);

        $payload = [
            'token' => $verification->token,
        ];

        $response = $this->postJson('/api/auth/token/verify', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Account already verified');
    });

    it('should verify token', function () {
        $verification = UserVerification::factory()->create([
            'expires_at' => now(),
        ]);

        $payload = [
            'token' => $verification->token,
        ];

        $response = $this->postJson('/api/auth/token/verify', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'firstname',
                    'lastname',
                    'email',
                    'status',
                    'email_verified_at',
                    'type',
                ],
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('User verified successfully')
            ->and($content->data->status)->toBe(UserEnum::ACTIVE->value);
    });
});

describe('resend token', function () {
    it('should return an error if email does not exist', function () {
        $payload = [
            'email' => 'invalid_email@email.com',
        ];

        $response = $this->postJson('/api/auth/token/resend', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The selected email is invalid.');
    });

    it('should return an error if user is already verified', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $payload = [
            'email' => $user->email,
        ];

        $response = $this->postJson('/api/auth/token/resend', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('User already verified');
    });

    it('should resend token to user', function () {
        Event::fake();

        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
        ];

        $response = $this->postJson('/api/auth/token/resend', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('Verification link resent successfully');

        Event::assertDispatched(VerificationMailResentEvent::class, function ($event) use ($user) {
            return $event->verification->user->id === $user->id;
        });
    });
});

describe('forgot password', function () {
    it('should return an error if email does not exist', function () {
        $payload = [
            'email' => 'invalid_email@email.com',
        ];

        $response = $this->postJson('/api/auth/forgot-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The selected email is invalid.');
    });

    it('should return an error if user is not verified', function () {
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
        ];

        $response = $this->postJson('/api/auth/forgot-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('User not yet verified');
    });

    it('should return an error if user is not active', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $payload = [
            'email' => $user->email,
        ];

        $response = $this->postJson('/api/auth/forgot-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('User not active');
    });

    it('should send forgot password mail to user', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => UserEnum::ACTIVE->value,
        ]);

        $payload = [
            'email' => $user->email,
        ];

        $response = $this->postJson('/api/auth/forgot-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('Forgot password link resent successfully');
    });

    it('should throw an error if forgot password mail is attempted to be run more than once in 1 minute', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => UserEnum::ACTIVE->value,
        ]);

        $payload = [
            'email' => $user->email,
        ];

        $this->postJson('/api/auth/forgot-password', $payload);
        $response = $this->postJson('/api/auth/forgot-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('You cannot send more than 1 password request per minute. Try again later.');
    });
});

describe('reset password', function () {
    it('should return an error if email does not exist', function () {
        $payload = [
            'email' => 'invalid_email@email.com',
        ];

        $response = $this->postJson('/api/auth/reset-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The selected email is invalid.');
    });

    it('should return an error if password does not match', function () {
        $user = User::factory()->create();

        PasswordResetToken::factory()->create([
            'email' => $user->email,
        ]);

        $payload = [
            'email' => $user->email,
            'token' => '6baad6f126fa53233f5120dd32225d4a9eeaea26dce58789f0b3b6efcdb0dadb',
            'password' => 'NewPassword@2025',
            'password_confirmation' => 'NewPassword',
        ];

        $response = $this->postJson('/api/auth/reset-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The password field confirmation does not match.');
    });

    it('should return an error if password is less than 8 characters', function () {
        $user = User::factory()->create();

        PasswordResetToken::factory()->create([
            'email' => $user->email,
        ]);

        $payload = [
            'email' => $user->email,
            'token' => '6baad6f126fa53233f5120dd32225d4a9eeaea26dce58789f0b3b6efcdb0dadb',
            'password' => 'NewPass',
            'password_confirmation' => 'NewPass',
        ];

        $response = $this->postJson('/api/auth/reset-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The password confirmation field must be at least 8 characters.');
    });

    it('should return an error if password does not contain numbers', function () {
        $user = User::factory()->create();

        PasswordResetToken::factory()->create([
            'email' => $user->email,
        ]);

        $payload = [
            'email' => $user->email,
            'token' => '6baad6f126fa53233f5120dd32225d4a9eeaea26dce58789f0b3b6efcdb0dadb',
            'password' => 'NewPassword@',
            'password_confirmation' => 'NewPassword@',
        ];

        $response = $this->postJson('/api/auth/reset-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The password confirmation field must contain at least one number.');
    });

    it('should return an error if password does not contain symbols', function () {
        $user = User::factory()->create();

        PasswordResetToken::factory()->create([
            'email' => $user->email,
        ]);

        $payload = [
            'email' => $user->email,
            'token' => '6baad6f126fa53233f5120dd32225d4a9eeaea26dce58789f0b3b6efcdb0dadb',
            'password' => 'NewPassword',
            'password_confirmation' => 'NewPassword',
        ];

        $response = $this->postJson('/api/auth/reset-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('The password confirmation field must contain at least one symbol.');
    });

    it('should return an error if user is not verified', function () {
        $user = User::factory()->create();

        PasswordResetToken::factory()->create([
            'email' => $user->email,
        ]);

        $payload = [
            'email' => $user->email,
            'token' => '6baad6f126fa53233f5120dd32225d4a9eeaea26dce58789f0b3b6efcdb0dadb',
            'password' => 'NewPassword@2025',
            'password_confirmation' => 'NewPassword@2025',
        ];

        $response = $this->postJson('/api/auth/reset-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('User not yet verified');
    });

    it('should return an error if user is not active', function () {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        PasswordResetToken::factory()->create([
            'email' => $user->email,
        ]);

        $payload = [
            'email' => $user->email,
            'token' => '6baad6f126fa53233f5120dd32225d4a9eeaea26dce58789f0b3b6efcdb0dadb',
            'password' => 'NewPassword@2025',
            'password_confirmation' => 'NewPassword@2025',
        ];

        $response = $this->postJson('/api/auth/reset-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('User not active');
    });

    it('should reset user password', function () {
        Event::fake();

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => UserEnum::ACTIVE->value,
        ]);

        PasswordResetToken::factory()->create([
            'email' => $user->email,
        ]);

        $payload = [
            'email' => $user->email,
            'token' => '6baad6f126fa53233f5120dd32225d4a9eeaea26dce58789f0b3b6efcdb0dadb',
            'password' => 'NewPassword@2025',
            'password_confirmation' => 'NewPassword@2025',
        ];

        $response = $this->postJson('/api/auth/reset-password', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'message',
            ]);
        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('Password successfully reset. You can login now');

        Event::assertDispatched(PasswordReset::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    });
});

describe('authenticated', function () {
    it('should return a 401 if unauthenticated', function () {
        $response = $this->getJson('/api/authenticated');
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        expect($content->message)->toBe('Unauthenticated.');
    });

    it('should throw an error if user is not yet verified', function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $response = $this->getJson('/api/authenticated');
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('User not yet verified');
    });

    it('should throw an error if user is not active', function () {
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($this->user);

        $response = $this->getJson('/api/authenticated');
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('User not active');
    });
});
