<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles (use firstOrCreate to avoid duplicates from migrations)
        Role::firstOrCreate(['name' => 'Applicant', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Scholar', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Committee Member', 'guard_name' => 'web']);
        $systemAdminRole = Role::firstOrCreate(['name' => 'System Admin', 'guard_name' => 'web']);

        // Create C. Nkunze as System Admin (only essential admin user)
        $adminUser = User::firstOrCreate(
            ['email' => 'c.nkunze@lgfug.org'],
            [
                'name' => 'C. Nkunze',
                'password' => bcrypt('password'), // Default password, should be changed
            ]
        );
        $adminUser->assignRole($systemAdminRole);
    }
}
