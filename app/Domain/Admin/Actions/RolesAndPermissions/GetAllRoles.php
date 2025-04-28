<?php

namespace App\Domain\Admin\Actions\RolesAndPermissions;

use App\Domain\Admin\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

class GetAllRoles
{
    public function execute(Request $request): AnonymousResourceCollection
    {
        if ($request->input('withPermissions') === 'true') {
            return RoleResource::collection(Role::with('permissions')->get());
        }

        return RoleResource::collection(Role::all());
    }
}
