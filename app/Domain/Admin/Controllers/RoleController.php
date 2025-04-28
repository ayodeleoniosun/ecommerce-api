<?php

namespace App\Domain\Admin\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Admin\Actions\RolesAndPermissions\AssignRolesToUser;
use App\Domain\Admin\Actions\RolesAndPermissions\GetAllPermissions;
use App\Domain\Admin\Actions\RolesAndPermissions\GetAllRoles;
use App\Domain\Admin\Actions\RolesAndPermissions\RevokeRoleFromUser;
use App\Domain\Admin\Requests\AssignRolesToUserRequest;
use App\Domain\Admin\Requests\RevokeRoleFromUserRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController
{
    public function __construct(
        private readonly GetAllRoles $getAllRoles,
        private readonly GetAllPermissions $getAllPermissions,
        private readonly AssignRolesToUser $assignRolesToUser,
        private readonly RevokeRoleFromUser $revokeRole,
    ) {}

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
}
