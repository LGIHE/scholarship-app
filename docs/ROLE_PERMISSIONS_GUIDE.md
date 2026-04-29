# Role & Permissions Management Guide

## Overview

The scholarship management system now includes a robust role-based access control (RBAC) system with granular permissions for different functionalities and views.

## Key Changes

### 1. Enhanced Role Management

The Role Management system now includes comprehensive permission controls organized into logical categories:

#### Permission Categories

1. **Application Management**
   - `application.view` - View individual applications
   - `application.view_any` - View all applications list
   - `application.create` - Create new applications
   - `application.edit` - Edit applications
   - `application.delete` - Delete applications
   - `application.approve` - Approve applications
   - `application.reject` - Reject applications
   - `application.review` - Review applications
   - `application.export` - Export application data

2. **Scholar Management**
   - `scholar.view` - View individual scholar details
   - `scholar.view_any` - View all scholars list
   - `scholar.create` - Create new scholar records
   - `scholar.edit` - Edit scholar information
   - `scholar.delete` - Delete scholar records
   - `scholar.view_bio` - View scholar bio section
   - `scholar.view_applications` - View scholar's applications
   - `scholar.view_progress` - View academic progress
   - `scholar.view_documents` - View uploaded documents
   - `scholar.edit_progress` - Edit academic progress records
   - `scholar.upload_documents` - Upload documents
   - `scholar.export` - Export scholar data

3. **User Management**
   - `user.view` - View individual users
   - `user.view_any` - View all users list
   - `user.create` - Create new users
   - `user.edit` - Edit user information
   - `user.delete` - Delete users
   - `user.manage_applicants` - Manage applicant users
   - `user.manage_system_users` - Manage system users
   - `user.export` - Export user data

4. **Role & Permission Management**
   - `role.view` - View roles
   - `role.create` - Create new roles
   - `role.edit` - Edit roles
   - `role.delete` - Delete roles
   - `permission.view` - View permissions
   - `permission.create` - Create permissions
   - `permission.edit` - Edit permissions
   - `permission.delete` - Delete permissions

5. **Dashboard & Reports**
   - `dashboard.view` - Access dashboard
   - `dashboard.view_stats` - View statistics widgets
   - `dashboard.view_charts` - View charts and graphs
   - `report.view` - View reports
   - `report.generate` - Generate new reports
   - `report.export` - Export reports

6. **System Settings**
   - `settings.view` - View system settings
   - `settings.edit` - Edit system settings
   - `settings.manage_email` - Manage email settings
   - `settings.manage_notifications` - Manage notification settings

### 2. Scholar Users Removed from Sidebar

The "Scholar Users" navigation item has been removed from the sidebar to simplify navigation. Scholar user information is now accessible through the main Scholars section.

### 3. Comprehensive Scholar View Page

When clicking "View" on a scholar in the Scholars table, a full-page view opens with the following sections:

#### Bio Tab
- **Personal Information**: Name, email, account creation date
- **Academic Information**: University, course/program, student ID, expected graduation date
- **Scholarship Details**: Scholarship start date, current status

#### Applications Tab
- **Original Application**: Application ID, status, submission date
- **Personal Information**: All personal details from the application
- **Financial Information**: Financial details submitted
- **Guardian Information**: Guardian/parent information
- **Essay**: Essay responses

#### Academic Progress Tab
- **Progress Records**: Semester-by-semester academic performance
  - Semester and year
  - CGPA with color-coded badges (Green: ≥3.5, Blue: ≥3.0, Yellow: ≥2.5)
  - Transcript documents with download links
  - Record date

#### Documents Tab
- **Transcripts**: All uploaded transcript documents organized by semester
- Direct download links for each document
- Document upload dates

#### Activity Tab
- **Timeline**: Key dates and events
  - Scholar record creation
  - Last update
  - Email verification status
- **Statistics**:
  - Total progress records
  - Average CGPA (color-coded)
  - Number of uploaded documents

## Default Role Permissions

### System Admin
- Has **all permissions** across the entire system
- Full access to all features and settings

### Committee Member
- Application viewing and review
- Scholar information viewing (all sections)
- Dashboard and report access
- Cannot modify system settings or manage users

### Example Custom Roles

#### Application Reviewer
```php
$reviewerRole->syncPermissions([
    'application.view',
    'application.view_any',
    'application.review',
    'dashboard.view',
    'dashboard.view_stats',
]);
```

#### Scholar Coordinator
```php
$coordinatorRole->syncPermissions([
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

## How to Use

### Creating a New Role

1. Navigate to **System Administration > Roles**
2. Click **New Role**
3. Enter the role name (e.g., "Application Reviewer")
4. Select permissions from the organized tabs:
   - Application Management
   - Scholar Management
   - User Management
   - Role & Permission Management
   - Dashboard & Reports
   - System Settings
5. Click **Create**

### Editing Role Permissions

1. Navigate to **System Administration > Roles**
2. Click **View** or **Edit** on the role you want to modify
3. Check or uncheck permissions as needed
4. Click **Save**

### Viewing Scholar Details

1. Navigate to **Scholar Management > Scholars**
2. Click **View** (eye icon) on any scholar
3. Browse through the tabs:
   - **Bio**: Personal and academic information
   - **Applications**: Original application details
   - **Academic Progress**: Semester-by-semester performance
   - **Documents**: All uploaded documents
   - **Activity**: Timeline and statistics

## Running the Demo Seeder

To create example roles with specific permissions:

```bash
php artisan db:seed --class=PermissionDemoSeeder
```

This will create:
- **Application Reviewer**: Limited to viewing and reviewing applications
- **Scholar Coordinator**: Focused on scholar management and progress tracking

## Technical Details

### Database Tables
- `permissions` - Stores all available permissions
- `roles` - Stores role definitions
- `role_has_permissions` - Links roles to permissions
- `model_has_roles` - Links users to roles

### Permission Checking in Code

```php
// Check if user has specific permission
if (auth()->user()->can('application.approve')) {
    // User can approve applications
}

// Check if user has any of multiple permissions
if (auth()->user()->hasAnyPermission(['scholar.edit', 'scholar.delete'])) {
    // User can edit or delete scholars
}

// Check if user has a specific role
if (auth()->user()->hasRole('System Admin')) {
    // User is a system admin
}
```

## Migration Information

The permissions were created via migration:
- **File**: `database/migrations/2026_04_28_053737_create_granular_permissions.php`
- **Run**: `php artisan migrate`

## Notes

- All permissions are automatically assigned to the **System Admin** role
- The **Committee Member** role receives a curated set of permissions for application review and scholar viewing
- Custom roles can be created with any combination of permissions
- Permissions are cached for performance - clear cache after changes: `php artisan optimize:clear`
- The Scholar Users resource is hidden from navigation but still accessible programmatically if needed

## Future Enhancements

Consider implementing:
- Permission-based UI element visibility
- Audit logging for permission changes
- Time-based permission grants
- Department or region-based access control
- Bulk permission assignment tools
