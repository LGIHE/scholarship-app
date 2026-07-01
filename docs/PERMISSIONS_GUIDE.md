# Roles & Permissions Guide

The system uses `spatie/laravel-permission` for role-based access control (RBAC) with granular permissions.

---

## Default Roles

| Role | Panel Access | Permissions |
|------|-------------|-------------|
| **System Admin** | Full Filament panel | All permissions |
| **Committee Member** | Filament panel | Application review, scholar viewing, dashboard/reports |
| **Applicant** | Portal only | Submit and view own application |
| **Scholar** | Portal only | View application status, log academic progress |

---

## Permission Reference

### Application Management
| Permission | Description |
|------------|-------------|
| `application.view` | View a single application |
| `application.view_any` | View the applications list |
| `application.create` | Create applications |
| `application.edit` | Edit applications |
| `application.delete` | Delete applications |
| `application.approve` | Approve applications |
| `application.reject` | Reject applications |
| `application.review` | Mark applications as under review |
| `application.export` | Export application data |

### Scholar Management
| Permission | Description |
|------------|-------------|
| `scholar.view` | View a single scholar |
| `scholar.view_any` | View the scholars list |
| `scholar.create` | Create scholar records |
| `scholar.edit` | Edit scholar information |
| `scholar.delete` | Delete scholar records |
| `scholar.view_bio` | View the Bio tab |
| `scholar.view_applications` | View the Applications tab |
| `scholar.view_progress` | View the Academic Progress tab |
| `scholar.view_documents` | View the Documents tab |
| `scholar.edit_progress` | Edit academic progress records |
| `scholar.upload_documents` | Upload documents |
| `scholar.export` | Export scholar data |

### User Management
| Permission | Description |
|------------|-------------|
| `user.view` | View a single user |
| `user.view_any` | View the users list |
| `user.create` | Create users |
| `user.edit` | Edit users |
| `user.delete` | Delete users |
| `user.manage_applicants` | Manage applicant users |
| `user.manage_system_users` | Manage system/admin users |
| `user.export` | Export user data |

### Role & Permission Management
| Permission | Description |
|------------|-------------|
| `role.view` / `role.create` / `role.edit` / `role.delete` | Manage roles |
| `permission.view` / `permission.create` / `permission.edit` / `permission.delete` | Manage permissions |

### Dashboard & Reports
| Permission | Description |
|------------|-------------|
| `dashboard.view` | Access the dashboard |
| `dashboard.view_stats` | View statistics widgets |
| `dashboard.view_charts` | View charts |
| `report.view` | View reports |
| `report.generate` | Generate reports |
| `report.export` | Export reports |

### System Settings
| Permission | Description |
|------------|-------------|
| `settings.view` | View settings |
| `settings.edit` | Edit settings |
| `settings.manage_email` | Manage email settings |
| `settings.manage_notifications` | Manage notifications |

---

## Managing Roles in the Admin Panel

### Create a New Role

1. Go to **System Administration → Roles**
2. Click **New Role**
3. Enter the role name
4. Select permissions from the tabbed permission groups
5. Click **Create**

### Edit Role Permissions

1. Go to **System Administration → Roles**
2. Click **Edit** on the target role
3. Check/uncheck permissions as needed
4. Click **Save**

> After changing permissions, clear the cache:
> ```bash
> php artisan optimize:clear
> ```

---

## Example Custom Roles

### Application Reviewer (read-only + review)

```php
$role->syncPermissions([
    'application.view',
    'application.view_any',
    'application.review',
    'dashboard.view',
    'dashboard.view_stats',
]);
```

### Scholar Coordinator (scholar management focus)

```php
$role->syncPermissions([
    'scholar.view',
    'scholar.view_any',
    'scholar.view_bio',
    'scholar.view_applications',
    'scholar.view_progress',
    'scholar.view_documents',
    'scholar.edit_progress',
    'scholar.upload_documents',
    'dashboard.view',
    'report.view',
    'report.generate',
]);
```

### Seed Demo Roles

```bash
php artisan db:seed --class=PermissionDemoSeeder
```

This creates the Application Reviewer and Scholar Coordinator example roles.

---

## Checking Permissions in Code

```php
// Check a specific permission
if (auth()->user()->can('application.approve')) { ... }

// Check any of multiple permissions
if (auth()->user()->hasAnyPermission(['scholar.edit', 'scholar.delete'])) { ... }

// Check a role
if (auth()->user()->hasRole('System Admin')) { ... }
```

---

## Database Tables (Spatie)

| Table | Purpose |
|-------|---------|
| `permissions` | All available permissions |
| `roles` | Role definitions |
| `role_has_permissions` | Role ↔ permission links |
| `model_has_roles` | User ↔ role links |

Permissions were created via:
`database/migrations/2026_04_28_053737_create_granular_permissions.php`
