# LGF Scholarship Management System — Project Documentation

> **Luigi Giussani Foundation (LGF)** — LIT Scholar Platform

---

## Overview

The LGF Scholarship Management System is a full-stack web application that manages the end-to-end scholarship lifecycle for the Luigi Giussani Foundation's **Leaders in Teaching (LIT) Program**. The program awards scholarships to female students pursuing BSc Education in STEM subjects at Ugandan universities, with a commitment to teaching in rural/underserved areas.

### Key Goals
- Streamline the scholarship application process with multi-step online forms
- Automate application scoring based on eligibility criteria
- Provide committee members with robust administrative and review dashboards
- Track scholar academic progress and renewal status
- Unify applicant and scholar experiences into a single portal

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend Framework** | Laravel 11 |
| **Frontend Integration**| Inertia.js |
| **Frontend UI** | React 18 + TypeScript |
| **Admin Dashboard** | Laravel Filament v3 |
| **Build Tool** | Vite |
| **Styling** | Tailwind CSS 3 + custom design tokens (HSL-based) |
| **UI Components** | shadcn/ui (Radix UI primitives) |
| **Animations** | Framer Motion |
| **Forms** | React Hook Form + Zod (Portal) / Filament Forms (Admin) |
| **Database** | PostgreSQL / MySQL |
| **Testing** | Pest (PHP) + Vitest (React) |

### Key Dependencies
- `framer-motion` — page transitions and micro-interactions
- `sonner` — toast notifications
- `lucide-react` — icon library for frontend / Blade Heroicons for Filament
- `spatie/laravel-permission` — Role and permission management

---

## Project Structure

```text
app/
├── Filament/            # Admin Panel (Committee Dashboard)
│   ├── Resources/       # Applications, Scholars, Users management
│   └── Widgets/         # Analytics and Stats for Committee
├── Http/
│   ├── Controllers/     # Inertia Controllers for Portal & Public
│   ├── Requests/        # Form Request Validation
│   └── Middleware/      # Role & Auth Middleware
├── Models/              # Eloquent Models (User, Application, Scholar, Progress)
└── Services/            # Business Logic (e.g., AutoScoringService)
resources/
├── css/                 # Tailwind entry & HSL custom properties
└── js/
    ├── Components/      # shadcn/ui and custom React components
    ├── Layouts/         # Inertia Layouts (PortalLayout, PublicLayout)
    ├── Pages/           # Inertia React Pages
    │   ├── Public/      # Landing Page (Index.tsx)
    │   ├── Portal/      # Unified applicant/scholar dashboard
    │   └── Auth/        # Login/Register pages
    ├── lib/             # Utility functions
    └── app.tsx          # Inertia + React setup
database/
├── migrations/          # Schema definitions
└── seeders/             # Database Seeders for testing
```

---

## User Roles & Authentication

### Roles

| Role | Access | Description |
|------|--------|-------------|
| **Applicant** | Portal Dashboard, Application Form, Status | Students applying for scholarships |
| **Scholar** | All Applicant views + My Progress | Approved applicants tracking academic progress |
| **Committee** | Filament Admin Dashboard | Foundation staff reviewing and managing applications |

### Authentication Flow
- **Registration:** Applicants must first create an account (Sign Up) before they can access the portal and begin their scholarship application.
- **Authentication:** Standard Laravel Auth (Session-based) via Laravel Breeze or Jetstream.
- **Authorization:** `spatie/laravel-permission` manages roles.
- **Applicant/Scholar Portal:** React-based Inertia frontend for students.
- **Committee Portal:** Laravel Filament backend (`/admin`). Completely separate from the student portal for maximum administrative flexibility.

### Unified Portal Architecture (Inertia/React)
The applicant and scholar views are combined into a **single progressive portal**:
- An applicant sees: Dashboard, My Application, Application Status.
- Once approved, their role is updated via Eloquent/Spatie, and they gain access to Scholar features (My Progress).
- The portal navigation dynamically updates based on the user's role injected via Inertia shared props.

---

## Core Functionalities

### 1. Multi-Step Application Form (React + Inertia)
A 5-step wizard built with React Hook Form and Framer Motion:
- **Step 1: Personal Info** | Demographics, program of study, CGPA, schools.
- **Step 2: Finances** | Estimated expenses, income sources, funding gap.
- **Step 3: Guardian Info** | Parent/Guardian details.
- **Step 4: Essay & Commitment** | Personal essay on STEM teaching.
- **Step 5: Review & Submit** | Summary and auto-score preview.
_Features:_ Auto-saving drafts to the database via pending Application records, Framer Motion step transitions.

### 2. Committee Dashboard (Filament Admin)
Built purely with Laravel Filament classes:
- **Filament Widgets:** Stat Cards (Total applications, approved, pending, active scholars, avg CGPA).
- **Charts:** Application submission trends and Score distributions using Filament Chart Widgets.
- **Filters:** Global scope filters for academic cycles (2023/2024, 2024/2025).

### 3. Application Review (Filament Resources)
Committee members use Filament's `ApplicationResource`:
- List view with advanced filters/search.
- View pages to see application data, auto-generated scores, and breakdowns.
- Custom Filament Actions to Approve, Reject, or Waitlist.

### 4. Scholar Management (Filament Resources)
Managed via Filament's `ScholarResource`:
- Filter by approval year, institution, and status.
- View associated academic progress and payment history via Filament Relation Managers.

### 5. Application Status Tracking (Inertia Portal)
Timeline-based status tracker for students showing:
- Draft → Submitted → Under Review → Decision.

### 6. Scholar Progress Tracking (Inertia Portal)
Scholars can log in to their React portal to:
- View their academic progress history.
- Submit new progress entries (semester, CGPA, uploads).

### 7. Landing Page (Inertia Portal)
Public-facing React page containing:
- Hero section with Framer Motion animations.
- Feature highlights and eligibility criteria.

---

## Auto-Scoring Engine

The scoring engine (`App\Services\ScoringService`) runs server-side to automatically evaluate applications on a **0-100 scale** upon submission.

### Scoring Breakdown

| Criterion | Max Score | Factors |
|-----------|-----------|---------|
| **Financial Need** | 30 | Expense-income gap ratio, guardian income level, scholarship type requested |
| **Academic Merit** | 25 | CGPA brackets (4.0+ = 25, 3.5+ = 20, 3.0+ = 15), STEM program bonus (+3) |
| **Demographics** | 15 | Female (+5), refugee/displaced (+5), disability (+5), target age range 18-35 (+2) |
| **Commitment** | 15 | Essay keyword analysis (teaching, rural, STEM, inclusive), essay length bonus |
| **Essay Quality** | 15 | Word count tiers, additional info bonus (+3) |

### Score Labels
Ranges determine automatic flagging: 80-100 (Excellent), 65-79 (Strong), 0-34 (Needs Review). The breakdown is saved to JSON columns in the database for later committee review.

---

## Design System & Styling

### Color System (HSL-based semantic tokens in Tailwind)
Both the React frontend and Filament admin utilize customized Tailwind tokens.
- `--primary`: `160 45% 22%` (Dark green)
- `--accent`: `42 85% 55%` (Gold)
*Filament Theme:* A custom Filament theme is generated to match the public portal's branding (Green and Gold).

### Custom CSS Properties & Animations
- **Framer Motion** handles frontend page entrances (`fadeUp` staggered delays).
- shared `shadcn/ui` components standardise UI on the frontend.

---

## Data Persistence

### Relational Database
Data is stored in PostgreSQL/MySQL using Eloquent ORM. Models include:
- `User`: Handles authentication and roles.
- `Application`: Stores JSON form data, drafted/submitted status, and scoring breakdowns.
- `Scholar`: Created upon application approval; tracks renewal status and bonds.
- `AcademicProgress`: Belongs to Scholar; tracks CGPA semester by semester.

---

## Payment Implementation

### Current Status: Not Yet Implemented
The platform currently does **not** process any payments. 

### Potential Integration via Laravel Cashier / Stripe
1. **Scholarship Disbursement:** Generate payout schedules in Filament.
2. **Donation Portal:** Stripe checkout in the generic React frontend.

---

## Routing & Navigation

### Route Structure (Web & Admin)

```text
# Public & Portal Routes (web.php -> Inertia)
/                          → Landing page (PublicController)
/login                     → Laravel Auth (Login)
/register                  → Laravel Auth (Applicant Registration)
/portal                    → PortalDashboardController
/portal/application        → ApplicationController (Multi-step form)
/portal/status             → ApplicationStatusController
/portal/progress           → ScholarProgressController

# Committee Routes (Filament)
/admin                     → Filament Dashboard (Analytics)
/admin/applications        → ApplicationResource
/admin/scholars            → ScholarResource
```

---

## Future Enhancements

- **Email/SMS Notifications:** Utilize Laravel Notifications/Queues for status updates.
- **PDF Generation:** Use `barryvdh/laravel-dompdf` for downloading submitted applications.
- **Automated Background Jobs:** Laravel Scheduler to flag scholars needing renewal.
