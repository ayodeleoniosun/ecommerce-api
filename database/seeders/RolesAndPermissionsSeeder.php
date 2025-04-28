<?php

namespace Database\Seeders;

use Kdabrow\SeederOnce\SeederOnce;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/data/roles-and-permissions.json'));
        $roles = json_decode($json, true);

        foreach ($roles as $role) {
            $createdRole = Role::firstOrCreate([
                'name' => $role['name'],
                'guard_name' => $role['guard_name'],
            ]);

            if (isset($role['permissions'])) {
                foreach ($role['permissions'] as $permission) {
                    $permission = Permission::firstOrCreate([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);

                    $createdRole->givePermissionTo($permission);
                }
            }
        }
    }
}
