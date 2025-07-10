<?php

namespace App\Domain\Admin\Actions\RolesAndPermissions;

use App\Application\Shared\Exceptions\BadRequestException;
use App\Domain\Auth\Interfaces\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RevokePermissionFromUserAction
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(Request $request): Collection
    {
        $user = $this->userRepository->findByColumn('uuid', $request->input('user_id'));

        $permission = $request->input('permission');

        throw_if(! $user->hasPermissionTo($permission), BadRequestException::class,
            'User does not have the permission to '.$permission);

        $user->revokePermissionTo($permission);

        return $user->getPermissionNames();
    }
}
