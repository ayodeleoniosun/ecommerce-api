<?php

namespace App\Domain\Admin\Actions\RolesAndPermissions;

use App\Application\Shared\Enum\UserEnum;
use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AssignRolesToUser
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(Request $request): Collection
    {
        $user = $this->userRepository->findByColumn('uuid', $request->input('role_user_id'));

        throw_if(! $user->email_verified_at, BadRequestException::class, 'User not yet verified');

        throw_if($user->status !== UserEnum::ACTIVE->value, BadRequestException::class, 'User not active');

        $user->syncRoles($request->input('roles'));

        return $user->getRoleNames();
    }
}
