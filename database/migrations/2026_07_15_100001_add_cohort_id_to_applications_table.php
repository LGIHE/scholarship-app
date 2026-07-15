<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Nullable so existing rows survive; the seeder fills them in.
            $table->foreignId('cohort_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained()
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Cohort::class);
            $table->dropColumn('cohort_id');
        });
    }
};
