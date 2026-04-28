<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Application Management
            'application.view',
            'application.view_any',
            'application.create',
            'application.edit',
            'application.delete',
            'application.approve',
            'application.reject',
            'application.review',
            'application.export',
            
            // Scholar Management
            'scholar.view',
            'scholar.view_any',
            'scholar.create',
            'scholar.edit',
            'scholar.delete',
            'scholar.view_bio',
            'scholar.view_applications',
            'scholar.view_progress',
            'scholar.view_documents',
            'scholar.edit_progress',
            'scholar.upload_documents',
            'scholar.export',
            
            // User Management
            'user.view',
            'user.view_any',
            'user.create',
            'user.edit',
            'user.delete',
            'user.manage_applicants',
            'user.manage_system_users',
            'user.export',
            
            // Role & Permission Management
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',
            
            // Dashboard & Reports
            'dashboard.view',
            'dashboard.view_stats',
            'dashboard.view_charts',
            'report.view',
            'report.generate',
            'report.export',
            
            // System Settings
            'settings.view',
            'settings.edit',
            'settings.manage_email',
            'settings.manage_notifications',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign all permissions to System Admin role
        $systemAdminRole = Role::where('name', 'System Admin')->first();
        if ($systemAdminRole) {
            $systemAdminRole->syncPermissions(Permission::all());
        }

        // Assign specific permissions to Committee Member role
        $committeeMemberRole = Role::where('name', 'Committee Member')->first();
        if ($committeeMemberRole) {
            $committeeMemberRole->syncPermissions([
                'application.view',
                'application.view_any',
                'application.review',
                'application.approve',
                'application.reject',
                'application.export',
                'scholar.view',
                'scholar.view_any',
                'scholar.view_bio',
                'scholar.view_applications',
                'scholar.view_progress',
                'scholar.view_documents',
                'dashboard.view',
                'dashboard.view_stats',
                'dashboard.view_charts',
                'report.view',
                'report.generate',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Delete all permissions
        Permission::whereIn('name', [
            'application.view', 'application.view_any', 'application.create', 'application.edit', 'application.delete',
            'application.approve', 'application.reject', 'application.review', 'application.export',
            'scholar.view', 'scholar.view_any', 'scholar.create', 'scholar.edit', 'scholar.delete',
            'scholar.view_bio', 'scholar.view_applications', 'scholar.view_progress', 'scholar.view_documents',
            'scholar.edit_progress', 'scholar.upload_documents', 'scholar.export',
            'user.view', 'user.view_any', 'user.create', 'user.edit', 'user.delete',
            'user.manage_applicants', 'user.manage_system_users', 'user.export',
            'role.view', 'role.create', 'role.edit', 'role.delete',
            'permission.view', 'permission.create', 'permission.edit', 'permission.delete',
            'dashboard.view', 'dashboard.view_stats', 'dashboard.view_charts',
            'report.view', 'report.generate', 'report.export',
            'settings.view', 'settings.edit', 'settings.manage_email', 'settings.manage_notifications',
        ])->delete();
    }
};
