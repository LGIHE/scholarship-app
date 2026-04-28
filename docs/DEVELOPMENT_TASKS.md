# Development Tasks & Roadmap

This document breaks down the LGF Scholarship Management System into actionable development phases using Laravel 11, Inertia.js (React), and Filament v3.

## Phase 1: Project Initialization & Setup
- [x] **Initialize Laravel:** Run `laravel new scholarship_app` (or use Composer).
- [x] **Install Jetstream/Breeze:** Setup Laravel Breeze with Inertia (React) stack for frontend scaffolding.
- [x] **Install Filament:** Require and install Filament v3 (`filament/filament`) for the admin panel.
- [x] **Install Spatie Permissions:** Run composer require `spatie/laravel-permission` and publish its migrations.
- [x] **Frontend Tooling:** 
  - Install `shadcn/ui` and its dependencies (Radix UI primitives, `lucide-react`, `clsx`, `tailwind-merge`).
  - Install `framer-motion` for animations.
  - Install `react-hook-form` and `zod` for frontend validations.
- [x] **Configure Tailwind:** Setup Tailwind CSS 3 with custom HSL-based design tokens (matching LGF's green and gold theme). Configure both the main Tailwind config for React and a custom theme for Filament.

## Phase 2: Database & Models Architecture
- [x] **Users & Roles:** Setup migrations/seeders for User model and standard roles (Applicant, Scholar, Committee).
- [x] **Applications Model:** Create `Application` model and migration. Should include JSON columns for form data (personal, financial, guardian, essay) and scoring breakdowns, plus a `status` enum (draft, submitted, under_review, approved, rejected).
- [x] **Scholars Model:** Create `Scholar` model and migration (belongs to User, optionally belongs to Application).
- [x] **Academic Progress Model:** Create `AcademicProgress` model and migration (belongs to Scholar) to track semester-by-semester CGPA.
- [x] **Relationships:** Define all Eloquent relationships across the models.
- [x] **Seeders:** Build database seeders with mock data to aid development (mock applicants, scholars, and applications).

## Phase 3: Authentication & Role Management
- [x] **Registration Flow:** Customize Breeze registration to assign the default `Applicant` role upon sign up.
- [x] **Login Routing:** Ensure successful login redirects:
  - `Committee` -> `/admin` (Filament)
  - `Applicant`/`Scholar` -> `/portal` (Inertia frontend)
- [x] **Middleware:** Create middleware to protect `/portal` routes and restrict `/admin` to only the Committee role.

## Phase 4: Applicant Portal & Application Form
- [x] **Portal Layout:** Build the `DashboardLayout.tsx` using `shadcn/ui` components (sidebar, header).
- [x] **Status Tracker:** Build the `ApplicationStatus` view (timeline component indicating draft vs submitted vs review state).
- [x] **Multi-Step Form (Frontend):**
  - Implement form shell with step navigation and progress bar.
  - Step 1: Personal Info
  - Step 2: Finances
  - Step 3: Guardian Info
  - Step 4: Essay & Commitment
  - Step 5: Review & Submit
- [x] **Draft Saving:** Create an API endpoint/Inertia route to auto-save the application payload to the database without triggering full validation.
- [x] **Form Submission:** Implement the final submission endpoint that validates the entire payload and changes status to `submitted`.

## Phase 5: Auto-Scoring Engine
- [x] **Create Scoring Service:** Build `App\Services\ScoringService`.
- [x] **Implement Logic:** Write the weighting algorithms:
  - Financial Need (30 pts)
  - Academic Merit (25 pts)
  - Demographics (15 pts)
  - Commitment (15 pts)
  - Essay Quality (15 pts)
- [x] **Trigger Scoring:** Hook the `ScoringService` into the Application submission controller, saving the final score and breakdown JSON to the `Application` record.

## Phase 6: Committee Dashboard (Filament)
- [x] **Dashboard Widgets:** Create Filament Stat Widgets (Total Apps, Pending, Approved, Avg Score).
- [x] **Application Resource:** 
  - Create `ApplicationResource` for the committee to list and view submissions.
  - Configure table filters (status, cycle, score ranges).
  - Design the View page to beautifully display the JSON application data and the scoring breakdown.
- [x] **Review Actions:** Add custom Filament bulk/individual actions to `Approve`, `Waitlist`, or `Reject` applications.
- [x] **Approval Logistics:** When `Approve` is clicked, automatically create a `Scholar` record for the user and update their role via Spatie.

## Phase 7: Scholar Management & Progress Tracking
- [x] **Scholar Resource (Filament):** Create `ScholarResource` for admin to view active scholars, track renewals, and view their progress.
- [x] **Academic Progress Relation Manager:** Add a Relation Manager in Filament so admins can see/edit a scholar's progress directly from the Scholar view.
- [x] **Scholar Portal (Inertia):** Update the React portal sidebar to conditionally reveal the "My Progress" tab if the user has the `Scholar` role.
- [x] **Progress Form:** Build a simple Inertia page/form for scholars to log their new semester CGPA and upload academic transcripts.

## Phase 8: Polish & Launch Readiness
- [x] **Public Landing Page:** Build the `/` route with Framer Motion animations, highlighting the program benefits and clear Call-To-Action buttons leading to `/register`.
- [x] **Notifications (Optional):** Setup email notifications (e.g., "Application Received", "Application Approved") using Laravel Mailables.
- [x] **Testing:** Write Pest tests for the `ScoringService` to ensure accuracy. Write basic tests for form submission and role restrictions.
- [x] **Deployment Prep:** Finalize `.env` settings, configure Vite for production, and setup a reliable database hosting solution.