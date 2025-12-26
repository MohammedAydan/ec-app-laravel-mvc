<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Optionally seed a test user if not exists
        if (!User::where('email', 'admin@gmail.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123')
            ]);

            // admin role
            Role::create([
                'name' => 'admin',
                'description' => 'Administrator role with full permissions',
            ]);

            // owner role
            Role::create([
                'name' => 'owner',
                'description' => 'Owner role with limited permissions',
            ]);

            // admin role permissions (create, read, update, delete)
            $adminRole = Role::where('name', 'admin')->first();
            Permission::create([
                'name' => 'create',
                'role_id' => $adminRole->id,
            ]);
            Permission::create([
                'name' => 'read',
                'role_id' => $adminRole->id,
            ]);
            Permission::create([
                'name' => 'update',
                'role_id' => $adminRole->id,
            ]);
            Permission::create([
                'name' => 'delete',
                'role_id' => $adminRole->id,
            ]);
            // owner role permissions (read only)
            $ownerRole = Role::where('name', 'owner')->first();
            Permission::create([
                'name' => 'read',
                'role_id' => $ownerRole->id,
            ]);

            // Assign admin role to the test user
            $user = User::where('email', 'admin@gmail.com')->first();
            $user->role_id = $adminRole->id;
            $user->save();

            $itemsSeeder = new ItemsSeeder();
            $itemsSeeder->run();
        }
    }
}
