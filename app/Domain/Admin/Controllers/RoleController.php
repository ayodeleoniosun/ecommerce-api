<?php

namespace App\Domain\Admin\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Admin\Actions\RolesAndPermissions\AssignPermissionsToUserAction;
use App\Domain\Admin\Actions\RolesAndPermissions\AssignRolesToUserAction;
use App\Domain\Admin\Actions\RolesAndPermissions\GetAllPermissionsAction;
use App\Domain\Admin\Actions\RolesAndPermissions\GetAllRolesAction;
use App\Domain\Admin\Actions\RolesAndPermissions\RevokePermissionFromUserAction;
use App\Domain\Admin\Actions\RolesAndPermissions\RevokeRoleFromUserAction;
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
        private readonly GetAllRolesAction $getAllRoles,
        private readonly GetAllPermissionsAction $getAllPermissions,
        private readonly AssignRolesToUserAction $assignRolesToUser,
        private readonly RevokeRoleFromUserAction $revokeRole,
        private readonly AssignPermissionsToUserAction $assignPermissionsToUser,
        private readonly RevokePermissionFromUserAction $revokePermissionFromUser,
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
