<?php

namespace Tests\Unit\Actions\Auth;

use App\Application\Actions\Auth\RegisterUser;
use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Events\Auth\UserRegisteredEvent;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Repositories\User\UserVerificationRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->userVerificationRepo = Mockery::Mock(UserVerificationRepository::class);
    Event::fake();
});

it('can register new user', function () {
    $payload = [
        'firstname' => 'john',
        'lastname' => 'doe',
        'email' => 'johndoe@xyz.com',
        'password' => '12345',
    ];

    $userEntity = new UserEntity($payload['firstname'], $payload['lastname'], $payload['email'], $payload['password']);
    $mockedUser = new User([
        'id' => 1,
        'firstname' => $userEntity->getFirstname(),
        'lastname' => $userEntity->getLastname(),
        'email' => $userEntity->getEmail(),
        'password' => Hash::make($userEntity->getPassword()),
    ]);

    $this->userRepo->shouldReceive('create')
        ->once()
        ->with($userEntity)
        ->andReturn($mockedUser);

    $registerUser = new RegisterUser($this->userRepo);
    $response = $registerUser->execute($userEntity);

    Event::assertDispatched(UserRegisteredEvent::class);

    expect($response)->toBeInstanceOf(User::class)
        ->and($response->firstname)->toBe($userEntity->getFirstname())
        ->and($response->lastname)->toBe($userEntity->getLastname())
        ->and($response->email)->toBe($userEntity->getEmail());
});
