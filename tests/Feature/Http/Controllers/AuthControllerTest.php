<?php

namespace Tests\Application\Actions\Auth;

use App\Application\Shared\Enum\UserEnum;
use App\Infrastructure\Models\User;
use Illuminate\Http\Response;

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
        ];

        $response = $this->postJson('/api/auth/register', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_CREATED);
        expect($content->success)->toBe(true)
            ->and($content->message)->toBe('User registered successfully')
            ->and($content->data->firstname)->toBe($payload['firstname'])
            ->and($content->data->lastname)->toBe($payload['lastname'])
            ->and($content->data->email)->toBe($payload['email']);
    });

    it('should return an error if email address already exist', function () {
        $this->user = User::factory()->create();

        $payload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone_number' => '08123456789',
            'email' => $this->user->email,
            'password' => 'Ayodele@2025',
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
        $this->user = User::factory()->create();

        $payload = [
            'email' => $this->user->email,
            'password' => 'Ayodele@2025',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Email not yet verified');
    });

    it('should return an error if user is inactive', function () {
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $payload = [
            'email' => $this->user->email,
            'password' => 'Ayodele@2025',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Account is inactive');
    });

    it('should return an error if login credentials is invalid', function () {
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => UserEnum::ACTIVE->value,
        ]);

        $payload = [
            'email' => $this->user->email,
            'password' => 'InvalidPassword',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        expect($content->success)->toBe(false)
            ->and($content->message)->toBe('Invalid login credentials');
    });

    it('should login successfully', function () {
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => UserEnum::ACTIVE->value,
        ]);

        $payload = [
            'email' => $this->user->email,
            'password' => 'Ayodele@2025',
        ];

        $response = $this->postJson('/api/auth/login', $payload);
        $content = json_decode($response->getContent());

        $response->assertStatus(Response::HTTP_OK);
        expect($content->success)->toBe(true)
            ->and($content->data->firstname)->toBe($this->user->firstname)
            ->and($content->data->lastname)->toBe($this->user->lastname)
            ->and($content->data->email)->toBe($this->user->email)
            ->and($content->data->status)->toBe($this->user->status);
    });
});
