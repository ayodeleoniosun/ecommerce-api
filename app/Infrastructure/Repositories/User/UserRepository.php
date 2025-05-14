<?php

namespace App\Infrastructure\Repositories\User;

use App\Application\Shared\Enum\UserStatusEnum;
use App\Domain\Auth\Dtos\CreateUserDto;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\User\User;
use App\Infrastructure\Models\User\UserVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly UserVerificationRepositoryInterface $userVerificationRepository) {}

    public function create(CreateUserDto $userDto): User
    {
        $user = null;

        DB::transaction(function () use (&$user, $userDto) {
            $user = User::create($userDto->toArray());

            $this->userVerificationRepository->create([
                'user_id' => $user->id,
                'token' => hash('sha256', Str::random(40)),
                'expires_at' => Carbon::now()->addHours(6),
            ]);
        });

        return $user;
    }

    public function findByColumn(string $field, string $value): ?User
    {
        return User::where($field, $value)->first();
    }

    public function verify(UserVerification $verification): User
    {
        DB::transaction(function () use ($verification) {
            $verification->verified_at = now();
            $verification->save();

            $verification->user->update([
                'status' => UserStatusEnum::ACTIVE->value,
                'email_verified_at' => now(),
            ]);
        });

        $verification->refresh();

        return $verification->user;
    }

    public function resetPassword(array $request): string
    {
        return Password::reset($request, function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->setRememberToken(Str::random(60));

            $user->save();
        });
    }
}
