<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Example: Create a custom role with specific permissions
        $reviewerRole = Role::firstOrCreate(['name' => 'Application Reviewer', 'guard_name' => 'web']);
        
        $reviewerRole->syncPermissions([
            'application.view',
            'application.view_any',
            'application.review',
            'dashboard.view',
            'dashboard.view_stats',
        ]);

        // Example: Create a Scholar Coordinator role
        $coordinatorRole = Role::firstOrCreate(['name' => 'Scholar Coordinator', 'guard_name' => 'web']);
        
        $coordinatorRole->syncPermissions([
            'scholar.view',
            'scholar.view_any',
            'scholar.view_bio',
            'scholar.view_applications',
            'scholar.view_progress',
            'scholar.view_documents',
            'scholar.edit_progress',
            'scholar.upload_documents',
            'dashboard.view',
            'report.view',
            'report.generate',
        ]);

        $this->command->info('Permission demo roles created successfully!');
        $this->command->info('- Application Reviewer: Can only view and review applications');
        $this->command->info('- Scholar Coordinator: Can manage scholar progress and documents');
    }
}
