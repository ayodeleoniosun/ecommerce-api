<?php

namespace Tests\Unit\Actions\Auth;

use App\Application\Actions\Auth\RegisterUser;
use App\Application\Shared\Enum\UserTypeEnum;
use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Events\UserRegisteredEvent;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Event;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->registerUser = new RegisterUser($this->userRepo);
});

it('can register new user', function () {
    Event::fake();

    $user = User::factory()->create();
    $userEntity = new UserEntity($user->firstname, $user->lastname, $user->email, 'password',
        UserTypeEnum::CUSTOMER->value);

    $this->userRepo->shouldReceive('create')
        ->once()
        ->with($userEntity)
        ->andReturn($user);

    $response = $this->registerUser->execute($userEntity);

    Event::assertDispatched(UserRegisteredEvent::class);

    expect($response)->toBeInstanceOf(User::class)
        ->and($response->firstname)->toBe($userEntity->getFirstname())
        ->and($response->lastname)->toBe($userEntity->getLastname())
        ->and($response->email)->toBe($userEntity->getEmail());
});
