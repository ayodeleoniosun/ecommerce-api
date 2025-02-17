<?php

namespace App\Infrastructure\Repositories\User;

use App\Application\Shared\Enum\UserEnum;
use App\Domain\Auth\Entities\User as UserEntity;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Auth\Interfaces\Repositories\UserVerificationRepositoryInterface;
use App\Infrastructure\Models\User;
use App\Infrastructure\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly UserVerificationRepositoryInterface $userVerificationRepository) {}

    public function create(UserEntity $user): array
    {
        $record = null;
        $token = hash('sha256', Str::random(40));

        DB::transaction(function () use (&$record, $user, $token) {
            $record = User::create([
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'password' => Hash::make($user->getPassword()),
            ]);

            $this->userVerificationRepository->create([
                'user_id' => $record->id,
                'token' => $token,
                'expires_at' => Carbon::now()->addHours(6),
            ]);
        });

        return [
            'user' => $record,
            'token' => $token,
        ];
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
