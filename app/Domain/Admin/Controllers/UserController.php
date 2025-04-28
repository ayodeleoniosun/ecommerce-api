<?php

namespace App\Domain\Admin\Controllers;

use App\Application\Shared\Responses\ApiResponse;
use App\Domain\Admin\Actions\RolesAndPermissions\GetAllRoles;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController
{
    public function __construct(
        private readonly GetAllRoles $getAllRoles,
    ) {}

    public function assignRoles(Request $request): JsonResponse
    {
        try {
            $data = $this->getAllRoles->execute($request);

            return ApiResponse::success('Roles successfully assigned to user', $data);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode());
        }
    }
}
