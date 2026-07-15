<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // e.g. "Cohort 1 — 2026/2027"
            $table->string('academic_year');                 // e.g. "2026/2027"
            $table->string('slug')->unique();                // e.g. "2026-2027"  (used in /scholarships/{slug})
            $table->unsignedInteger('scholarships_available')->default(0);
            $table->dateTime('opens_at')->nullable();        // when applications open
            $table->dateTime('closes_at')->nullable();       // application deadline (replaces APPLICATION_DEADLINE)
            $table->boolean('is_active')->default(false);    // only ONE cohort is active at a time
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};
