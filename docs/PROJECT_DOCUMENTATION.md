# LGF Scholarship Management System — Project Documentation

> **Luigi Giussani Foundation (LGF)** — LIT Scholar Platform

---

## Overview

The LGF Scholarship Management System manages the end-to-end scholarship lifecycle for the Luigi Giussani Foundation's **Leaders in Teaching (LIT) Program**. The program awards scholarships to female students pursuing BSc Education in STEM subjects at Ugandan universities, with a commitment to teaching in rural/underserved areas.

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
| **Frontend Integration** | Inertia.js |
| **Frontend UI** | React 18 + TypeScript |
| **Admin Dashboard** | Laravel Filament v3 |
| **Build Tool** | Vite |
| **Styling** | Tailwind CSS 3 + custom HSL design tokens |
| **UI Components** | shadcn/ui (Radix UI primitives) |
| **Animations** | Framer Motion |
| **Forms** | React Hook Form + Zod (Portal) / Filament Forms (Admin) |
| **Database** | PostgreSQL / MySQL |
| **Testing** | Pest (PHP) + Vitest (React) |
| **Permissions** | spatie/laravel-permission |
| **Email** | Resend (transactional), queued via Laravel Jobs |

---

## Project Structure

```
app/
├── Filament/            # Admin Panel (Committee Dashboard)
│   ├── Resources/       # Applications, Scholars, Users management
│   └── Widgets/         # Analytics and Stats
├── Http/
│   ├── Controllers/     # Inertia Controllers for Portal & Public
│   ├── Requests/        # Form Request Validation
│   └── Middleware/      # Role & Auth Middleware
├── Models/              # Eloquent Models
└── Services/            # Business Logic (AutoScoringService, etc.)
resources/
├── css/                 # Tailwind entry & HSL custom properties
└── js/
    ├── Components/      # shadcn/ui and custom React components
    ├── Layouts/         # Inertia Layouts (PortalLayout, PublicLayout)
    ├── Pages/           # Inertia React Pages
    │   ├── Public/      # Landing Page
    │   ├── Portal/      # Applicant/Scholar dashboard
    │   └── Auth/        # Login/Register pages
    └── app.tsx          # Inertia + React setup
database/
├── migrations/          # Schema definitions
└── seeders/             # Database Seeders
```

---

## User Roles & Authentication

| Role | Access | Description |
|------|--------|-------------|
| **System Admin** | Full Filament panel | Full system access |
| **Committee Member** | Filament panel | Reviews and manages applications |
| **Applicant** | Portal (`/portal`) | Students submitting applications |
| **Scholar** | Portal + Progress | Approved applicants tracking progress |

### Authentication Flow
- Registration creates an Applicant account via Laravel Breeze (session-based).
- Login redirects to `/admin` (Filament) for admin roles, or `/portal` (Inertia/React) for applicants/scholars.
- Authorization is managed by `spatie/laravel-permission`.

---

## Core Functionalities

### Multi-Step Application Form (React + Inertia)
A 5-step wizard with auto-save to the database:
1. **Personal Info** — Demographics, program, CGPA, schools
2. **Finances** — Expenses, income sources, funding gap
3. **Guardian Info** — Parent/Guardian details
4. **Essay & Commitment** — Personal essay on STEM teaching
5. **Review & Submit** — Summary and auto-score preview

### Committee Dashboard (Filament Admin)
- Stat widgets: total applications, approved, pending, active scholars, avg CGPA
- Charts: status distribution, applications by district/gender/university/nationality
- Application review with Approve / Reject / Waitlist actions
- Scholar management with academic progress relation managers
- Export functionality via `ApplicationsExport`

### Application Status Tracking
Timeline for students: Draft → Submitted → Under Review → Decision.

### Scholar Progress Tracking
Scholars log semester CGPA, upload transcripts, and view their history via the portal.

---

## Auto-Scoring Engine

`App\Services\ScoringService` evaluates applications on a **0–100 scale** at submission.

| Criterion | Max | Factors |
|-----------|-----|---------|
| Financial Need | 30 | Expense-income gap, guardian income, scholarship type |
| Academic Merit | 25 | CGPA brackets, STEM program bonus |
| Demographics | 15 | Female, refugee/displaced, disability, target age |
| Commitment | 15 | Essay keyword analysis (teaching, rural, STEM) |
| Essay Quality | 15 | Word count tiers, additional info bonus |

Score labels: **80–100** Excellent · **65–79** Strong · **0–34** Needs Review.
Breakdown is saved to a JSON column for committee review.

---

## System Architecture

### Authentication & Routing Flow

```
Login
 ├── Admin role (System Admin / Committee Member) → /admin (Filament)
 └── Applicant / Scholar → /portal (Inertia/React)
```

### Application Lifecycle

```
Draft → Submitted → Under Review → Approved / Rejected
                                        │
                                        └── Scholar record created automatically
                                            + Scholar role assigned
                                            + Welcome email sent
```

### Data Relationships

```
User
 ├── Application (HasMany)
 └── Scholar (HasOne)
      └── AcademicProgress (HasMany)

User ←→ Roles (Many-to-Many via Spatie Permission)
```

### Security Layers

| Layer | Implementation |
|-------|---------------|
| Authentication | Laravel Breeze, session-based |
| Authorization | Spatie Laravel Permission (RBAC) |
| Route Protection | `auth`, `EnsureApplicantOrScholar`, Filament `Authenticate` middleware |
| Resource Security | Filtered `getEloquentQuery()`, role-specific forms, conditional actions |
| Data Protection | bcrypt passwords, CSRF, Eloquent ORM (SQL injection prevention) |

---

## Design System

- **Primary color**: `160 45% 22%` (Dark green)
- **Accent color**: `42 85% 55%` (Gold)
- Filament admin theme matches the portal branding.
- Framer Motion handles page transitions and micro-interactions.
- shadcn/ui components standardise the React frontend.

---

## Route Structure

```
# Public & Portal (web.php → Inertia)
/                       Landing page
/login                  Authentication
/register               Applicant registration
/portal                 Dashboard
/portal/application     Multi-step form
/portal/status          Application status
/portal/progress        Scholar academic progress

# Admin (Filament)
/admin                  Dashboard + analytics
/admin/applications     ApplicationResource
/admin/scholars         ScholarResource
/admin/system-users     SystemUserResource
/admin/applicant-users  ApplicantUserResource
/admin/roles            RoleResource
```

---

## Future Enhancements

- **PDF Generation**: `barryvdh/laravel-dompdf` for downloadable applications
- **Automated Jobs**: Laravel Scheduler to flag scholars needing renewal
- **Payment/Disbursement**: Laravel Cashier / Stripe integration
- **Donation Portal**: Stripe checkout on the public landing page
