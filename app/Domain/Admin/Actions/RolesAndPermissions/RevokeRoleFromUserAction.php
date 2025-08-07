<?php

namespace App\Domain\Admin\Actions\RolesAndPermissions;

use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use App\Infrastructure\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RevokeRoleFromUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(Request $request): Collection
    {
        $user = $this->userRepository->findByColumn(User::class, 'uuid', $request->input('user_id'));

        $user->removeRole($request->input('role'));

        return $user->getRoleNames();
    }
}
