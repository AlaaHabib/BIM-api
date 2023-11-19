<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Create a user and assign the "admin" role
        $adminUser = User::updateOrCreate([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole('admin');

        // Create a user and assign the "user" role
        $generalUser = User::updateOrCreate([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => bcrypt('password'),
        ]);
        $generalUser->assignRole('user');
    }
}
