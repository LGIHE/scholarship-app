<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure all required roles exist
        $roles = [
            'Applicant',
            'Scholar',
            'Committee Member',
            'System Admin',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't delete roles on rollback as they might be in use
        // If you need to remove them, do it manually
    }
};
