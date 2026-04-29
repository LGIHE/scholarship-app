<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing 'Committee' role to 'Committee Member' if it exists
        $committeeRole = Role::where('name', 'Committee')->first();
        if ($committeeRole) {
            $committeeRole->update(['name' => 'Committee Member']);
        }
        
        // Create System Admin role if it doesn't exist
        if (!Role::where('name', 'System Admin')->exists()) {
            Role::create(['name' => 'System Admin', 'guard_name' => 'web']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'Committee Member' back to 'Committee'
        $committeeMemberRole = Role::where('name', 'Committee Member')->first();
        if ($committeeMemberRole) {
            $committeeMemberRole->update(['name' => 'Committee']);
        }
        
        // Remove System Admin role
        $systemAdminRole = Role::where('name', 'System Admin')->first();
        if ($systemAdminRole) {
            $systemAdminRole->delete();
        }
    }
};
