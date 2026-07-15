<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Cohort;
use Illuminate\Database\Seeder;

class CohortSeeder extends Seeder
{
    public function run(): void
    {
        // Create Cohort 1 only if it doesn't already exist (idempotent).
        $cohort = Cohort::firstOrCreate(
            ['slug' => '2026-2027'],
            [
                'name'                   => 'Cohort 1 — 2026/2027',
                'academic_year'          => '2026/2027',
                'slug'                   => '2026-2027',
                'scholarships_available' => 400,
                'opens_at'               => '2026-01-01 00:00:00',
                'closes_at'              => '2026-07-15 23:59:59',
                'is_active'              => true,
                'description'            => '400 scholarships for pre-service female STEM student teachers — Ugandan citizens, refugees, and young women with disabilities — admitted to pursue a Bachelor of Science with Education (BScEd) for the 2026/2027 academic year.',
            ]
        );

        // Ensure this is the only active cohort.
        Cohort::where('id', '!=', $cohort->id)->update(['is_active' => false]);

        // Assign ALL existing applications (regardless of status) to Cohort 1.
        // Only updates rows that don't already have a cohort assigned.
        $updated = Application::whereNull('cohort_id')->update(['cohort_id' => $cohort->id]);

        $this->command->info("Cohort 1 seeded (id: {$cohort->id}). {$updated} application(s) assigned.");
    }
}
