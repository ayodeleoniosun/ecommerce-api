<?php

namespace App\Application\Actions\Auth;

use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Application\Shared\Exceptions\ResourceNotFoundException;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginUser
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function execute(array $credentials): User
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        $this->validateUserStatus($user);

        $this->validatePassword($user, $credentials['password']);

        return $user;
    }

    private function validateUserStatus(?User $user): void
    {
        throw_if(! $user, ResourceNotFoundException::class, 'User not found');

        throw_if(! $user->email_verified_at, BadRequestException::class, 'Email not yet verified');

        $isActive = $user->status === UserEnum::ACTIVE->value;

        throw_if(! $isActive, BadRequestException::class, 'Account is inactive');
    }

    private function validatePassword(User $user, string $password): void
    {
        $validatePassword = Hash::check($password, $user->password);

        throw_if(! $validatePassword, BadRequestException::class, 'Invalid login credentials');
    }
}
