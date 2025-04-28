<?php

namespace App\Domain\Admin\Actions\RolesAndPermissions;

use App\Domain\Admin\Resources\PermissionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Permission;

class GetAllPermissions
{
    public function execute(): AnonymousResourceCollection
    {
        return PermissionResource::collection(Permission::all());
    }
}
