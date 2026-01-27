<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin', // Note: Spatie usually uses roles table, not a column
        ]);

        $permissions = [
            'can create user',
            'can edit user',
            'can delete user',
            'can view user',

            'can view dashboard',

            'can manage preset',

            'can create department',
            'can edit department',
            'can view department',
            'can delete department',
            
            'can create task',
            'can edit task',
            'can view task',
            'can delete task',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign ALL permissions to the user at once
        $user->givePermissionTo($permissions);
    }
}
