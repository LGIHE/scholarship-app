<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds new JSON columns for disability, dependants, and declaration sections
     * introduced by the updated LiT Uganda application form (2026/2027).
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (! Schema::hasColumn('applications', 'disability_info')) {
                $table->json('disability_info')->nullable()->after('personal_info');
            }
            if (! Schema::hasColumn('applications', 'dependants_info')) {
                $table->json('dependants_info')->nullable()->after('disability_info');
            }
            if (! Schema::hasColumn('applications', 'declaration_info')) {
                $table->json('declaration_info')->nullable()->after('guardian_info');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumnIfExists('disability_info');
            $table->dropColumnIfExists('dependants_info');
            $table->dropColumnIfExists('declaration_info');
        });
    }
};
