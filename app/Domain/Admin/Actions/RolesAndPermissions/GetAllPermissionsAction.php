<?php

namespace App\Domain\Admin\Actions\RolesAndPermissions;

use App\Domain\Admin\Resources\RolesAndPermission\PermissionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Permission;

class GetAllPermissionsAction
{
    public function execute(): AnonymousResourceCollection
    {
        return PermissionResource::collection(Permission::all());
    }
}
