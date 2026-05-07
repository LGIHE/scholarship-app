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
        Schema::table('academic_progress', function (Blueprint $table) {
            // Rename 'year' to 'academic_year' and change type to string
            $table->string('academic_year', 20)->after('scholar_id')->nullable();
            
            // Add new columns
            $table->decimal('gpa', 4, 2)->after('semester')->nullable();
            $table->text('courses_taken')->after('cgpa')->nullable();
            $table->text('achievements')->after('courses_taken')->nullable();
            $table->text('challenges')->after('achievements')->nullable();
            $table->text('notes')->after('challenges')->nullable();
        });

        // Copy data from 'year' to 'academic_year' if there's existing data
        DB::statement('UPDATE academic_progress SET academic_year = CAST(year AS CHAR) WHERE year IS NOT NULL');

        // Drop the old 'year' column
        Schema::table('academic_progress', function (Blueprint $table) {
            $table->dropColumn('year');
        });

        // Drop transcript_path if it exists (not used in current implementation)
        if (Schema::hasColumn('academic_progress', 'transcript_path')) {
            Schema::table('academic_progress', function (Blueprint $table) {
                $table->dropColumn('transcript_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_progress', function (Blueprint $table) {
            // Add back the old columns
            $table->integer('year')->after('semester')->nullable();
            $table->string('transcript_path')->after('cgpa')->nullable();
            
            // Drop the new columns
            $table->dropColumn([
                'academic_year',
                'gpa',
                'courses_taken',
                'achievements',
                'challenges',
                'notes',
            ]);
        });
    }
};
