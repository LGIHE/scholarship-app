# Changelog

All notable changes to the LGF Scholarship Management System.

---

## [Current] — April 2026

### Navigation & Structure
- Reorganised admin sidebar into three logical groups: **Application Management**, **Scholar Management**, **System Administration**
- Removed Scholar Users from sidebar (accessible via Scholar record cross-link)
- Each user now appears in exactly one resource — no more duplication between Applicant Users and Scholar Users views
- Added cross-navigation buttons: "View Applications" from Applicants, "View Scholar Details" from Scholar Users, "View User Account" from Scholars

### Scholars
- Comprehensive Scholar view page with tabbed sections: Bio, Applications, Academic Progress, Documents, Activity
- CGPA colour-coded badges: green ≥3.5, blue ≥3.0, yellow ≥2.5
- Academic Progress relation manager added to Scholar resource

### Permissions & Roles
- Implemented granular permission system across 6 categories: Application Management, Scholar Management, User Management, Role & Permission Management, Dashboard & Reports, System Settings
- System Admin automatically receives all permissions
- Committee Member receives curated permissions for review and scholar viewing
- `PermissionDemoSeeder` added for example Application Reviewer and Scholar Coordinator roles

### Email Notifications
- Integrated Resend for transactional emails (replaces SMTP)
- Emails queued for performance
- Added: Welcome email on registration, application received confirmation, status update notifications (Under Review, Approved, Rejected), system user creation with credentials

### UI & Visual
- Custom Filament theme aligned with portal branding (dark green + gold)
- Logo placement updated across admin panel and portal
- Password show/hide toggle added to login and registration forms
- Modal dialogs for key admin actions (approve, reject, status change)

### Dashboard Widgets
- Added charts: Applications by District, Gender, Nationality, University, Science vs Arts
- Scholar Stats widget updated: Active Scholars, Pending Applicants, Complete Profiles
- Recent Applications widget shows last 5 submissions

### Authentication & Password Reset
- Fixed Filament password reset routing (separate flow from portal reset)
- Custom `RequestPasswordReset` and `ResetPassword` pages for the admin panel
- Signature validation fix for password reset links

### Bug Fixes & Stability
- Fixed application submission flow (submission sometimes failed silently)
- Fixed role assignment on application approval
- Normalised district and institution names via Artisan commands (`NormaliseDistricts`, `NormaliseInstitutions`)
- Fixed individual permissions not persisting after role save
- Resolved database real-data field mapping issues

### Infrastructure
- Added `ApplicationsExport` (Excel/CSV export via Laravel Excel)
- Added `.htaccess` for shared hosting compatibility
- Apache and shared hosting setup documented
- Added `ADMIN_SYSTEM_UPDATES.md` (now consolidated here)

---

## Planned / Future

- PDF generation for downloadable applications (`barryvdh/laravel-dompdf`)
- Laravel Scheduler for automated scholar renewal flagging
- Payment/disbursement module (Laravel Cashier / Stripe)
- Donation portal on public landing page
- Audit logging for permission changes
- Time-based and region-based access control options
