# Permission-Based Navigation Guide

## Overview

The scholarship management system now implements **permission-based navigation visibility**. Users will only see menu items and actions they have permission to access.

## How It Works

### Navigation Visibility

Each resource (Applications, Scholars, Users, Roles) checks if the current user has the required permission before displaying in the sidebar:

- **No Permission** = Menu item is **hidden**
- **Has Permission** = Menu item is **visible**

### Action Visibility

Individual actions (View, Edit, Delete, Approve, etc.) also check permissions:

- **No Permission** = Action button is **hidden**
- **Has Permission** = Action button is **visible**

## Permission Checks by Resource

### 1. Applications Resource

**Navigation Visibility:**
- Requires: `application.view_any`
- If user doesn't have this permission, "Applications" won't appear in sidebar

**Actions:**
- **View**: Requires `application.view`
- **Edit**: Requires `application.edit`
- **Under Review**: Requires `application.review`
- **Approve**: Requires `application.approve`
- **Reject**: Requires `application.reject`
- **Create**: Requires `application.create`
- **Delete**: Requires `application.delete`

### 2. Scholars Resource

**Navigation Visibility:**
- Requires: `scholar.view_any`
- If user doesn't have this permission, "Scholars" won't appear in sidebar

**Actions:**
- **View**: Requires `scholar.view`
- **Edit**: Requires `scholar.edit`
- **Create**: Requires `scholar.create`
- **Delete**: Requires `scholar.delete`

### 3. Applicants (Applicant Users)

**Navigation Visibility:**
- Requires: `user.manage_applicants` OR `user.view_any`
- If user doesn't have either permission, "Applicants" won't appear in sidebar

**Actions:**
- **View**: Requires `user.view`
- **Edit**: Requires `user.edit`
- **Create**: Requires `user.create`
- **Delete**: Requires `user.delete`

### 4. System Users

**Navigation Visibility:**
- Requires: `user.manage_system_users` OR `user.view_any`
- If user doesn't have either permission, "System Users" won't appear in sidebar

**Actions:**
- **View**: Requires `user.view`
- **Edit**: Requires `user.edit`
- **Create**: Requires `user.create`
- **Delete**: Requires `user.delete`

### 5. Roles

**Navigation Visibility:**
- Requires: `role.view`
- If user doesn't have this permission, "Roles" won't appear in sidebar

**Actions:**
- **View**: Requires `role.view`
- **Edit**: Requires `role.edit`
- **Create**: Requires `role.create`
- **Delete**: Requires `role.delete`

### 6. Dashboard

**Access:**
- Requires: `dashboard.view`
- If user doesn't have this permission, they cannot access the dashboard

**Widgets:**
- **Application Stats Widget**: Requires `dashboard.view_stats`
- **Scholar Stats Widget**: Requires `dashboard.view_stats`
- **Applications by Status Chart**: Requires `dashboard.view_charts`
- **Recent Applications Widget**: Requires `dashboard.view_stats`

## Example Scenarios

### Scenario 1: Application Reviewer Role

**Permissions Granted:**
- `application.view`
- `application.view_any`
- `application.review`
- `dashboard.view`
- `dashboard.view_stats`

**What They See:**
- ✅ Dashboard (with stats widgets)
- ✅ Applications menu item
- ✅ View button on applications
- ✅ "Under Review" button on applications
- ❌ Edit button (no permission)
- ❌ Approve/Reject buttons (no permission)
- ❌ Scholars menu item (no permission)
- ❌ Users menu items (no permission)
- ❌ Roles menu item (no permission)

### Scenario 2: Scholar Coordinator Role

**Permissions Granted:**
- `scholar.view`
- `scholar.view_any`
- `scholar.view_bio`
- `scholar.view_progress`
- `scholar.view_documents`
- `scholar.edit_progress`
- `dashboard.view`

**What They See:**
- ✅ Dashboard
- ✅ Scholars menu item
- ✅ View button on scholars
- ❌ Edit button on scholars (no scholar.edit permission)
- ❌ Applications menu item (no permission)
- ❌ Users menu items (no permission)
- ❌ Roles menu item (no permission)

### Scenario 3: Committee Member (Default)

**Permissions Granted:**
- `application.view`, `application.view_any`, `application.review`, `application.approve`, `application.reject`
- `scholar.view`, `scholar.view_any`, `scholar.view_bio`, `scholar.view_applications`, `scholar.view_progress`, `scholar.view_documents`
- `dashboard.view`, `dashboard.view_stats`, `dashboard.view_charts`
- `report.view`, `report.generate`

**What They See:**
- ✅ Dashboard (with all widgets)
- ✅ Applications menu item
- ✅ View, Review, Approve, Reject buttons on applications
- ✅ Scholars menu item
- ✅ View button on scholars
- ❌ Edit/Delete buttons (no permission)
- ❌ Create buttons (no permission)
- ❌ Users menu items (no permission)
- ❌ Roles menu item (no permission)

### Scenario 4: System Admin (Default)

**Permissions Granted:**
- ALL 47 permissions

**What They See:**
- ✅ Everything - full access to all features

### Scenario 5: User with No Permissions

**Permissions Granted:**
- None

**What They See:**
- ❌ No menu items in sidebar
- ❌ Cannot access dashboard
- ❌ Cannot access any resources
- Will see empty sidebar or access denied messages

## Navigation Structure Examples

### Full Access (System Admin)
```
📊 Dashboard

📝 Application Management
   ├── Applications
   └── Applicants

🎓 Scholar Management
   └── Scholars

⚙️ System Administration
   ├── System Users
   └── Roles
```

### Committee Member
```
📊 Dashboard

📝 Application Management
   └── Applications

🎓 Scholar Management
   └── Scholars
```

### Application Reviewer
```
📊 Dashboard

📝 Application Management
   └── Applications
```

### Scholar Coordinator
```
📊 Dashboard

🎓 Scholar Management
   └── Scholars
```

### No Permissions
```
(Empty sidebar - no menu items visible)
```

## Testing Permission-Based Navigation

### Test 1: Create a Limited Role

1. Log in as System Admin
2. Go to **System Administration > Roles**
3. Create a new role: "Application Viewer"
4. Grant only these permissions:
   - `application.view`
   - `application.view_any`
   - `dashboard.view`
5. Save the role

### Test 2: Assign Role to Test User

1. Go to **System Administration > System Users**
2. Create a new user or edit existing
3. Assign the "Application Viewer" role
4. Save

### Test 3: Log in as Test User

1. Log out from System Admin
2. Log in as the test user
3. Verify you only see:
   - Dashboard
   - Applications menu item
4. Verify you DON'T see:
   - Scholars
   - Users
   - Roles
5. Open an application
6. Verify you only see "View" button, not Edit/Delete/Approve/Reject

### Test 4: Verify Action Visibility

1. As the test user, go to Applications
2. Click on an application
3. Verify you can view but cannot edit
4. Try to access edit URL directly (should be denied)

## Technical Implementation

### Resource Level

Each resource implements these methods:

```php
public static function canViewAny(): bool
{
    return auth()->user()->can('permission.name');
}

public static function canCreate(): bool
{
    return auth()->user()->can('permission.name');
}

public static function canEdit($record): bool
{
    return auth()->user()->can('permission.name');
}

public static function canDelete($record): bool
{
    return auth()->user()->can('permission.name');
}

public static function canView($record): bool
{
    return auth()->user()->can('permission.name');
}
```

### Action Level

Actions check permissions using `->visible()`:

```php
Tables\Actions\EditAction::make()
    ->visible(fn () => auth()->user()->can('resource.edit')),
```

### Widget Level

Widgets check permissions using `canView()`:

```php
public static function canView(): bool
{
    return auth()->user()->can('dashboard.view_stats');
}
```

## Troubleshooting

### Menu items not hiding?

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

### User can still access via URL?

- Check that the resource has `canViewAny()` method implemented
- Verify the permission is correctly assigned to the role
- Clear browser cache and cookies

### Actions still visible?

- Check that actions have `->visible()` method with permission check
- Clear Filament cache: `php artisan filament:clear-cached-components`

### Dashboard widgets showing when they shouldn't?

- Verify widget has `canView()` method
- Check that user has `dashboard.view_stats` or `dashboard.view_charts` permission

## Best Practices

1. **Always grant view permissions together**: If granting `resource.edit`, also grant `resource.view` and `resource.view_any`

2. **Test with multiple roles**: Create test users with different roles to verify navigation visibility

3. **Document custom roles**: Keep track of which permissions each custom role has

4. **Use permission groups**: When creating roles, think in terms of job functions (Reviewer, Coordinator, etc.)

5. **Regular audits**: Periodically review role permissions to ensure they're still appropriate

## Security Notes

- Navigation hiding is a **UI convenience**, not a security measure
- Backend permission checks are still enforced at the resource level
- Users cannot bypass permissions by accessing URLs directly
- All actions are protected by permission checks
- Filament automatically handles authorization failures

## Summary

✅ **Navigation items** only show if user has `view_any` permission
✅ **Action buttons** only show if user has specific action permission
✅ **Dashboard widgets** only show if user has dashboard permissions
✅ **URL access** is protected by resource-level permission checks
✅ **Automatic enforcement** - no manual checks needed in most cases

The system now provides a clean, role-appropriate interface for each user type!
