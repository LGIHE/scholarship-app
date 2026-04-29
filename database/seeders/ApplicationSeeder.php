<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\AcademicProgress;
use Spatie\Permission\Models\Role;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // No demo data - all applications and scholars will come from real user registrations
        // This seeder is kept for future use if needed
    }
}
