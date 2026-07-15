<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    private array $cohortPermissions = [
        'cohort.view',
        'cohort.view_any',
        'cohort.create',
        'cohort.edit',
        'cohort.delete',
    ];

    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->cohortPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // System Admin gets all permissions (including the new ones).
        $systemAdmin = Role::where('name', 'System Admin')->first();
        if ($systemAdmin) {
            $systemAdmin->givePermissionTo($this->cohortPermissions);
        }
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::whereIn('name', $this->cohortPermissions)->delete();
    }
};
