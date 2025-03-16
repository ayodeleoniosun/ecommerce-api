<?php

namespace Tests\Unit\Actions\Auth;

use App\Application\Actions\Auth\RegisterUser;
use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Events\Auth\UserRegisteredEvent;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Event;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    Event::fake();
});

it('can register new user', function () {
    $user = User::factory()->create();
    $userEntity = new UserEntity($user->firstname, $user->lastname, $user->email, 'password');

    $this->userRepo->shouldReceive('create')
        ->once()
        ->with($userEntity)
        ->andReturn($user);

    $registerUser = new RegisterUser($this->userRepo);
    $response = $registerUser->execute($userEntity);

    Event::assertDispatched(UserRegisteredEvent::class);

    expect($response)->toBeInstanceOf(User::class)
        ->and($response->firstname)->toBe($userEntity->getFirstname())
        ->and($response->lastname)->toBe($userEntity->getLastname())
        ->and($response->email)->toBe($userEntity->getEmail());
});
