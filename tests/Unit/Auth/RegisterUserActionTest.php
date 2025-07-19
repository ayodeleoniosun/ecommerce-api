<?php

namespace Tests\Unit\Auth;

use App\Domain\Auth\Actions\RegisterUserAction;
use App\Domain\Auth\Dtos\CreateUserDto;
use App\Domain\Auth\Enums\UserTypeEnum;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Notifications\RegistrationCompletedNotification;
use App\Infrastructure\Models\User\User;
use Illuminate\Support\Facades\Notification;
use Mockery;

beforeEach(function () {
    $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
    $this->registerUser = new RegisterUserAction($this->userRepo);
});

it('can register new user', function () {
    Notification::fake();

    $user = User::factory()->create();
    $userDto = new CreateUserDto($user->firstname, $user->lastname, $user->email, 'password',
        UserTypeEnum::CUSTOMER->value);

    $this->userRepo->shouldReceive('create')
        ->once()
        ->with($userDto)
        ->andReturn($user);

    $response = $this->registerUser->execute($userDto);

    Notification::assertSentTo($user, RegistrationCompletedNotification::class);

    expect($response)->toBeInstanceOf(User::class)
        ->and($response->firstname)->toBe($user->firstname)
        ->and($response->lastname)->toBe($user->lastname)
        ->and($response->email)->toBe($user->email);
});
