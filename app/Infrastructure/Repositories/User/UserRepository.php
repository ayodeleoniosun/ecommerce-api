<?php

namespace App\Infrastructure\Repositories\User;

use App\Application\Shared\Enum\UserEnum;
use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\Auth\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly UserVerificationRepositoryInterface $userVerificationRepository) {}

    public function create(UserEntity $userEntity): User
    {
        $user = null;

        DB::transaction(function () use (&$user, $userEntity) {
            $user = User::create([
                'firstname' => $userEntity->getFirstname(),
                'lastname' => $userEntity->getLastname(),
                'email' => $userEntity->getEmail(),
                'password' => Hash::make($userEntity->getPassword()),
            ]);

            $this->userVerificationRepository->create([
                'user_id' => $user->id,
                'token' => hash('sha256', Str::random(40)),
                'expires_at' => Carbon::now()->addHours(6),
            ]);
        });

        return $user->with('verification')->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function verify(UserVerification $verification): User
    {
        DB::transaction(function () use ($verification) {
            $verification->verified_at = now();
            $verification->save();

            $verification->user->update([
                'status' => UserEnum::ACTIVE->value,
                'email_verified_at' => now(),
            ]);
        });

        $verification->refresh();

        return $verification->user;
    }
}
