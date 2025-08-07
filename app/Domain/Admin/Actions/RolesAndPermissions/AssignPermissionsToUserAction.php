<?php

namespace App\Domain\Admin\Actions\RolesAndPermissions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Auth\Enums\UserStatusEnum;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AssignPermissionsToUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(Request $request): Collection
    {
        $user = $this->userRepository->findByColumn(User::class, 'uuid', $request->input('user_id'));

        throw_if(! $user->email_verified_at, BadRequestException::class, 'User not yet verified');

        throw_if($user->status !== UserStatusEnum::ACTIVE->value, BadRequestException::class, 'User not active');

        $user->givePermissionTo($request->input('permissions'));

        return $user->getPermissionNames();
    }
}
