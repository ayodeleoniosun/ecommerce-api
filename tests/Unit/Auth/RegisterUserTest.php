<?php

namespace Tests\Unit\Actions\Auth;

use App\Application\Actions\Auth\RegisterUser;
use App\Application\Shared\Enum\UserTypeEnum;
use App\Domain\Auth\Dtos\UserDto;
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
    $userDto = new UserDto($user->firstname, $user->lastname, $user->email, 'password',
        UserTypeEnum::CUSTOMER->value);

    $this->userRepo->shouldReceive('create')
        ->once()
        ->with($userDto)
        ->andReturn($user);

    $response = $this->registerUser->execute($userDto);

    Event::assertDispatched(UserRegisteredEvent::class);

    expect($response)->toBeInstanceOf(User::class)
        ->and($response->firstname)->toBe($user->firstname)
        ->and($response->lastname)->toBe($user->lastname)
        ->and($response->email)->toBe($user->email);
});
