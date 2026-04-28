# System Architecture - Role-Based Access Control

## Authentication Flow

```
┌─────────────────────────────────────────────────────────────┐
│                      User Login                              │
│                   (AuthenticatedSessionController)           │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │  Check User Roles    │
              └──────────┬───────────┘
                         │
         ┌───────────────┴───────────────┐
         │                               │
         ▼                               ▼
┌────────────────────┐         ┌────────────────────┐
│   Admin Roles?     │         │  Applicant Roles?  │
│ - System Admin     │         │ - Applicant        │
│ - Committee Member │         │ - Scholar          │
└────────┬───────────┘         └────────┬───────────┘
         │                               │
         ▼                               ▼
┌────────────────────┐         ┌────────────────────┐
│  Redirect to:      │         │  Redirect to:      │
│  /admin            │         │  /portal           │
│  (Filament Panel)  │         │  (Application UI)  │
└────────────────────┘         └────────────────────┘
```

## Filament Admin Panel Structure

```
┌─────────────────────────────────────────────────────────────┐
│                    FILAMENT ADMIN PANEL                      │
│                        (/admin)                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                         DASHBOARD                            │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────────────┐  ┌──────────────────────┐        │
│  │ Application Stats    │  │  Scholar Stats       │        │
│  │ - Total Apps         │  │ - Active Scholars    │        │
│  │ - Pending Review     │  │ - Total Applicants   │        │
│  │ - Approved           │  │ - New Scholars       │        │
│  │ - Average Score      │  │                      │        │
│  └──────────────────────┘  └──────────────────────┘        │
│                                                              │
│  ┌──────────────────────────────────────────────┐          │
│  │    Applications by Status Chart              │          │
│  │         (Doughnut Chart)                     │          │
│  └──────────────────────────────────────────────┘          │
│                                                              │
│  ┌──────────────────────────────────────────────┐          │
│  │    Recent Applications Table                 │          │
│  │  (Last 5 applications with details)          │          │
│  └──────────────────────────────────────────────┘          │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    NAVIGATION SIDEBAR                        │
├─────────────────────────────────────────────────────────────┤
│  📊 Dashboard                                                │
│                                                              │
│  📚 SCHOLARSHIP MANAGEMENT                                   │
│     📄 Applications                                          │
│     🎓 Scholars                                              │
│                                                              │
│  👥 USER MANAGEMENT                                          │
│     👤 System Users                                          │
│     👥 Applicant Users                                       │
│     🛡️  Roles                                                │
└─────────────────────────────────────────────────────────────┘
```

## Database Role Structure

```
┌─────────────────────────────────────────────────────────────┐
│                         ROLES TABLE                          │
├─────────────────────────────────────────────────────────────┤
│  ID  │  Name              │  Guard   │  Description         │
├──────┼────────────────────┼──────────┼──────────────────────┤
│  1   │  System Admin      │  web     │  Full system access  │
│  2   │  Committee Member  │  web     │  Review & manage     │
│  3   │  Applicant         │  web     │  Submit applications │
│  4   │  Scholar           │  web     │  Approved applicants │
└─────────────────────────────────────────────────────────────┘
```

## User Management Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      USER MANAGEMENT                         │
└─────────────────────────────────────────────────────────────┘
                         │
         ┌───────────────┴───────────────┐
         │                               │
         ▼                               ▼
┌────────────────────┐         ┌────────────────────┐
│  SYSTEM USERS      │         │  APPLICANT USERS   │
│  Resource          │         │  Resource          │
├────────────────────┤         ├────────────────────┤
│ Filtered Query:    │         │ Filtered Query:    │
│ - System Admin     │         │ - Applicant        │
│ - Committee Member │         │ - Scholar          │
├────────────────────┤         ├────────────────────┤
│ Features:          │         │ Features:          │
│ - Create           │         │ - Create           │
│ - Edit             │         │ - Edit             │
│ - Delete           │         │ - Delete           │
│ - Role Assignment  │         │ - Role Assignment  │
│ - Password Mgmt    │         │ - Password Mgmt    │
└────────────────────┘         │ - View App Count   │
                               └────────────────────┘
```

## Application Workflow

```
┌─────────────────────────────────────────────────────────────┐
│                   APPLICATION LIFECYCLE                      │
└─────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │  Draft   │  ← Applicant creates application
    └────┬─────┘
         │
         ▼
    ┌──────────┐
    │Submitted │  ← Applicant submits for review
    └────┬─────┘
         │
         ▼
    ┌──────────────┐
    │ Under Review │  ← Committee marks for review
    └────┬─────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌─────────┐ ┌─────────┐
│Approved │ │Rejected │
└────┬────┘ └─────────┘
     │
     ▼
┌──────────────────────┐
│ Scholar Record       │
│ Created Automatically│
│ + Role Assigned      │
│ + Email Sent         │
└──────────────────────┘
```

## Resource Relationships

```
┌─────────────────────────────────────────────────────────────┐
│                    DATA RELATIONSHIPS                        │
└─────────────────────────────────────────────────────────────┘

    ┌──────────┐
    │   User   │
    └────┬─────┘
         │
    ┌────┴────────────────────┐
    │                         │
    ▼                         ▼
┌─────────────┐      ┌──────────────┐
│ Application │      │   Scholar    │
│ (HasMany)   │      │   (HasOne)   │
└─────────────┘      └───────┬──────┘
                             │
                             ▼
                    ┌──────────────────┐
                    │ Academic Progress│
                    │    (HasMany)     │
                    └──────────────────┘

    ┌──────────┐
    │   User   │
    └────┬─────┘
         │
         ▼
    ┌──────────┐
    │  Roles   │  (Many-to-Many via Spatie Permission)
    └──────────┘
```

## Security Layers

```
┌─────────────────────────────────────────────────────────────┐
│                      SECURITY LAYERS                         │
└─────────────────────────────────────────────────────────────┘

Layer 1: Authentication
├─ Laravel Breeze (Web Guard)
└─ Session-based authentication

Layer 2: Authorization
├─ Spatie Laravel Permission
├─ Role-based access control
└─ canAccessPanel() method

Layer 3: Route Protection
├─ auth middleware
├─ EnsureApplicantOrScholar middleware
└─ Filament's Authenticate middleware

Layer 4: Resource-Level Security
├─ Filtered queries (getEloquentQuery)
├─ Role-specific forms
└─ Conditional actions

Layer 5: Data Protection
├─ Password hashing (bcrypt)
├─ CSRF protection
└─ SQL injection prevention (Eloquent ORM)
```

## Widget Data Flow

```
┌─────────────────────────────────────────────────────────────┐
│                      WIDGET DATA FLOW                        │
└─────────────────────────────────────────────────────────────┘

┌──────────────────┐
│   Dashboard      │
│   Page           │
└────────┬─────────┘
         │
         ├─► ApplicationStatsWidget
         │   └─► Query: Application::count(), status filters
         │
         ├─► ScholarStatsWidget
         │   └─► Query: Scholar::count(), User::whereHas('roles')
         │
         ├─► ApplicationsByStatusChart
         │   └─► Query: Application::where('status', $status)->count()
         │
         └─► RecentApplicationsWidget
             └─► Query: Application::latest()->limit(5)
```

## File Organization

```
app/
├── Filament/
│   ├── Pages/
│   │   └── Dashboard.php (Custom dashboard)
│   │
│   ├── Resources/
│   │   ├── ApplicationResource.php
│   │   ├── ScholarResource.php
│   │   ├── SystemUserResource.php
│   │   ├── ApplicantUserResource.php
│   │   └── RoleResource.php
│   │
│   └── Widgets/
│       ├── ApplicationStatsWidget.php
│       ├── ScholarStatsWidget.php
│       ├── ApplicationsByStatusChart.php
│       └── RecentApplicationsWidget.php
│
├── Http/
│   ├── Controllers/
│   │   └── Auth/
│   │       └── AuthenticatedSessionController.php
│   │
│   └── Middleware/
│       └── EnsureApplicantOrScholar.php
│
├── Models/
│   ├── User.php (HasRoles trait, canAccessPanel)
│   ├── Application.php
│   └── Scholar.php
│
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php
```

## Key Design Decisions

### 1. Separation of Concerns
- **System Users** and **Applicant Users** are managed separately
- Different resources for different user types
- Clear navigation grouping

### 2. Role-Based Routing
- Login redirect based on user role
- Prevents unauthorized access
- Seamless user experience

### 3. Widget Architecture
- Modular widget design
- Reusable components
- Easy to add/remove widgets

### 4. Data Filtering
- Resources show only relevant data
- Query-level filtering for performance
- Role-specific views

### 5. Security First
- Multiple security layers
- Role-based access control
- Middleware protection

---

**Architecture Version**: 2.0
**Last Updated**: April 28, 2026
