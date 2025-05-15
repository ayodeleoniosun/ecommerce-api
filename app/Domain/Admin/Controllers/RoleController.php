<?php

namespace App\Domain\Admin\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Admin\Actions\RolesAndPermissions\AssignPermissionsToUser;
use App\Domain\Admin\Actions\RolesAndPermissions\AssignRolesToUser;
use App\Domain\Admin\Actions\RolesAndPermissions\GetAllPermissions;
use App\Domain\Admin\Actions\RolesAndPermissions\GetAllRoles;
use App\Domain\Admin\Actions\RolesAndPermissions\RevokePermissionFromUser;
use App\Domain\Admin\Actions\RolesAndPermissions\RevokeRoleFromUser;
use App\Domain\Admin\Requests\RolesAndPermissions\AssignPermissionsToUserRequest;
use App\Domain\Admin\Requests\RolesAndPermissions\AssignRolesToUserRequest;
use App\Domain\Admin\Requests\RolesAndPermissions\RevokePermissionFromUserRequest;
use App\Domain\Admin\Requests\RolesAndPermissions\RevokeRoleFromUserRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    public function __construct(
        private readonly GetAllRoles $getAllRoles,
        private readonly GetAllPermissions $getAllPermissions,
        private readonly AssignRolesToUser $assignRolesToUser,
        private readonly RevokeRoleFromUser $revokeRole,
        private readonly AssignPermissionsToUser $assignPermissionsToUser,
        private readonly RevokePermissionFromUser $revokePermissionFromUser,
    ) {
        //        if (!auth()->user()->hasRole(RoleEnum::SUPER_ADMIN)) {
        //            return ApiResponse::error('You do not have the permission to assign roles to users',
        //                Response::HTTP_FORBIDDEN);
        //        }
    }

    public function roles(Request $request): JsonResponse
    {
        try {
            $data = $this->getAllRoles->execute($request);

            return ApiResponse::success('Roles successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function permissions(): JsonResponse
    {
        try {
            $data = $this->getAllPermissions->execute();

            return ApiResponse::success('Permissions successfully retrieved', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function assignRoles(AssignRolesToUserRequest $request): JsonResponse
    {
        try {
            $data = $this->assignRolesToUser->execute($request);

            return ApiResponse::success('Roles successfully assigned to user', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function revokeRole(RevokeRoleFromUserRequest $request): JsonResponse
    {
        try {
            $data = $this->revokeRole->execute($request);

            return ApiResponse::success('Roles successfully revoked from user', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function assignPermissions(AssignPermissionsToUserRequest $request): JsonResponse
    {
        try {
            $data = $this->assignPermissionsToUser->execute($request);

            return ApiResponse::success('Permissions successfully assigned to user', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }

    public function revokePermission(RevokePermissionFromUserRequest $request): JsonResponse
    {
        try {
            $data = $this->revokePermissionFromUser->execute($request);

            return ApiResponse::success('Permissions successfully revoked from user', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
