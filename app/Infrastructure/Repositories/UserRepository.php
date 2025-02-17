<?php

namespace App\Infrastructure\Repositories;

use App\Application\Shared\Enum\UserEnum;
use App\Domain\User\Entities\User\User as UserEntity;
use App\Domain\User\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{

    public function create(UserEntity $user): User
    {
        $record = null;

        DB::transaction(function () use (&$record, $user) {
            $record = User::create([
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
            ]);

            UserVerification::create([
                'user_id' => $record->id,
                'token' => hash('sha256', Str::random(40)),
                'expires_at' => Carbon::now()->addHours(6),
            ]);
        });

        return $record;
    }

    public function getToken(string $token)
    {
        return UserVerification::with('user')
            ->where('token', $token)
            ->first();
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
