<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cohorts', function (Blueprint $table) {
            // Optional public-facing deadline date shown on /scholarships and the
            // applicant portal. When null the system falls back to closes_at.
            // This NEVER affects submission enforcement — closes_at always governs that.
            $table->date('display_closes_at')
                  ->nullable()
                  ->after('closes_at')
                  ->comment('Public-facing deadline shown to applicants. Null = use closes_at.');
        });
    }

    public function down(): void
    {
        Schema::table('cohorts', function (Blueprint $table) {
            $table->dropColumn('display_closes_at');
        });
    }
};
