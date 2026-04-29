<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $committeeRole = Role::where('name', 'Committee')->first();

        if (!$committeeRole) {
            $this->command->error('Committee role does not exist. Please run RoleAndUserSeeder first.');
            return;
        }

        // Check if user already exists
        $existingUser = User::where('email', 'c.nkunze@lgfug.org')->first();
        
        if ($existingUser) {
            $this->command->info('User with email c.nkunze@lgfug.org already exists.');
            return;
        }

        $adminUser = User::create([
            'name' => 'C. Nkunze',
            'email' => 'c.nkunze@lgfug.org',
            'password' => Hash::make('password'), // Default password
        ]);
        
        $adminUser->assignRole($committeeRole);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: c.nkunze@lgfug.org');
        $this->command->info('Password: password');
        $this->command->warn('Please change the password after first login.');
    }
}
