<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholar_id')->constrained()->cascadeOnDelete();
            $table->string('semester');
            $table->integer('year');
            $table->decimal('cgpa', 4, 2);
            $table->string('transcript_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_progress');
    }
};
