<?php

namespace App\Domain\Admin\Actions\RolesAndPermissions;

use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RevokeRoleFromUser
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(Request $request): Collection
    {
        $user = $this->userRepository->findByColumn('uuid', $request->input('role_user_id'));

        $user->removeRole($request->input('role'));

        return $user->getRoleNames();
    }
}
