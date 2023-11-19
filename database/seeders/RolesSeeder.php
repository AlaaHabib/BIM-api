<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Create "admin" role
        $adminRole = Role::updateOrCreate(['name' => 'admin']);

        // Create "user" role
        $userRole = Role::updateOrCreate(['name' => 'user']);

        // Define permissions
        $admin_permissions = [
            'create',
            'view',
            'edit',
            'delete',
            'record_payment',
            'report'
        ];
        $user_permissions = [
            'view',
        ];

        // Assign permissions to roles
        foreach ($admin_permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($permission);
            if (in_array($permission, $user_permissions));
            $userRole->givePermissionTo($permission);
        }
    }
}
