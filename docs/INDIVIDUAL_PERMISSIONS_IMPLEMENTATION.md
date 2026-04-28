# Individual Permissions Implementation

## Overview
Added the ability to assign individual permissions to users beyond their role-based permissions. This provides fine-grained access control for specific users who need additional capabilities.

## How It Works

### Permission Hierarchy
1. **Role Permissions** (Base): Every user gets permissions from their assigned role
2. **Direct Permissions** (Additional): Individual permissions can be added on top of role permissions
3. **Combined Access**: User has access to both role permissions AND direct permissions

### Example
- **User**: John Doe
- **Role**: Committee Member
  - Role permissions: application.view, application.review, application.approve
- **Direct Permissions**: user.create, user.edit
- **Total Access**: application.view, application.review, application.approve, user.create, user.edit

## Features Added

### 1. Additional Permissions Section

A new collapsible section in the user form:
- **Title**: "Additional Permissions"
- **Description**: Explains that permissions are added to (not replacing) role permissions
- **Component**: CheckboxList with grouped permissions
- **Layout**: 3 columns, searchable, bulk toggleable
- **State**: Collapsed by default to keep form clean

### 2. Permission Display

Permissions are displayed as a flat, searchable list:
- **Alphabetically sorted** by permission name
- **Prefix-based organization**: Permissions naturally group by prefix (e.g., all `application.*` permissions appear together)
- **Searchable**: Quickly find specific permissions
- **Bulk toggleable**: Select/deselect all at once

Example permission list:
- application.approve
- application.create
- application.delete
- application.edit
- application.export
- application.reject
- application.review
- application.view
- application.view_any
- dashboard.view
- dashboard.view_charts
- dashboard.view_stats
- scholar.create
- scholar.delete
- scholar.edit
- (and so on...)

### 3. Table Column

Added "Direct Permissions" column to user tables:
- Shows badges for assigned direct permissions
- Limit display to 2-3 badges (configurable)
- Hover tooltip shows all permissions
- Toggleable (can be hidden)
- Color-coded by resource type

### 4. Modal Width Adjustment

Increased modal width from `2xl` to `4xl` to accommodate the permissions section comfortably.

## Files Modified

### Resources
1. **app/Filament/Resources/SystemUserResource.php**
   - Added "Additional Permissions" section
   - Added permissions column to table
   - Increased modal width to 4xl

2. **app/Filament/Resources/ApplicantUserResource.php**
   - Added "Additional Permissions" section
   - Added permissions column to table
   - Increased modal width to 4xl

3. **app/Filament/Resources/ScholarUserResource.php**
   - Added "Additional Permissions" section
   - Added permissions column to table
   - Increased modal width to 4xl

### List Pages
1. **app/Filament/Resources/SystemUserResource/Pages/ListSystemUsers.php**
   - Updated modal width to 4xl

2. **app/Filament/Resources/ApplicantUserResource/Pages/ListApplicantUsers.php**
   - Updated modal width to 4xl

3. **app/Filament/Resources/ScholarUserResource/Pages/ListScholarUsers.php**
   - Updated modal width to 4xl

## Usage Guide

### Assigning Individual Permissions

#### Step 1: Open User Form
1. Go to any user list (System Users, Applicants, or Scholar Users)
2. Click "New" to create or click edit icon on existing user

#### Step 2: Set Primary Role
1. Fill in user information (name, email, password)
2. Select the user's primary role
   - This determines their base permissions

#### Step 3: Add Individual Permissions
1. Expand the "Additional Permissions" section
2. Browse permissions grouped by category
3. Check the boxes for permissions you want to grant
4. Use search to find specific permissions quickly
5. Use "Select All" / "Deselect All" for bulk operations

#### Step 4: Save
1. Click "Create" or "Save"
2. User now has both role permissions AND direct permissions

### Viewing User Permissions

#### In Table View
- Look at the "Direct Permissions" column
- Hover over badges to see all permissions
- Toggle column visibility if needed

#### In Edit Form
- Open user edit modal
- Expand "Additional Permissions" section
- Checked boxes show currently assigned permissions

### Use Cases

#### 1. Temporary Access
Grant a Committee Member temporary user management access:
- **Role**: Committee Member
- **Direct Permissions**: user.view, user.edit
- **Use Case**: Help with user account issues during peak application period

#### 2. Limited Admin
Create a user with limited admin capabilities:
- **Role**: Committee Member
- **Direct Permissions**: settings.view, settings.edit
- **Use Case**: Manage email templates without full admin access

#### 3. Cross-Department Access
Grant an Applicant user some scholar viewing permissions:
- **Role**: Applicant
- **Direct Permissions**: scholar.view, scholar.view_bio
- **Use Case**: Alumni who want to mentor current scholars

#### 4. Audit Access
Grant read-only access across all modules:
- **Role**: Committee Member
- **Direct Permissions**: All `.view` permissions
- **Use Case**: Auditor who needs to review all data

## Permission Categories

### Application Management
- `application.view` - View single application
- `application.view_any` - View all applications
- `application.create` - Create new application
- `application.edit` - Edit application
- `application.delete` - Delete application
- `application.approve` - Approve application
- `application.reject` - Reject application
- `application.review` - Review application
- `application.export` - Export applications

### Scholar Management
- `scholar.view` - View single scholar
- `scholar.view_any` - View all scholars
- `scholar.create` - Create scholar record
- `scholar.edit` - Edit scholar details
- `scholar.delete` - Delete scholar
- `scholar.view_bio` - View scholar biography
- `scholar.view_applications` - View scholar's applications
- `scholar.view_progress` - View academic progress
- `scholar.view_documents` - View uploaded documents
- `scholar.edit_progress` - Edit academic progress
- `scholar.upload_documents` - Upload documents
- `scholar.export` - Export scholar data

### User Management
- `user.view` - View single user
- `user.view_any` - View all users
- `user.create` - Create new user
- `user.edit` - Edit user details
- `user.delete` - Delete user
- `user.manage_applicants` - Manage applicant users
- `user.manage_system_users` - Manage system users
- `user.export` - Export user data

### Role & Permission Management
- `role.view` - View roles
- `role.create` - Create new role
- `role.edit` - Edit role
- `role.delete` - Delete role
- `permission.view` - View permissions
- `permission.create` - Create permission
- `permission.edit` - Edit permission
- `permission.delete` - Delete permission

### Dashboard & Reports
- `dashboard.view` - Access dashboard
- `dashboard.view_stats` - View statistics widgets
- `dashboard.view_charts` - View chart widgets
- `report.view` - View reports
- `report.generate` - Generate new reports
- `report.export` - Export reports

### System Settings
- `settings.view` - View system settings
- `settings.edit` - Edit system settings
- `settings.manage_email` - Manage email settings
- `settings.manage_notifications` - Manage notifications

## Technical Details

### CheckboxList Configuration

```php
Forms\Components\CheckboxList::make('permissions')
    ->relationship('permissions', 'name')
    ->options(function () {
        return \Spatie\Permission\Models\Permission::all()
            ->pluck('name', 'id')
            ->toArray();
    })
    ->columns(3)
    ->gridDirection('row')
    ->bulkToggleable()
    ->searchable()
```

### Table Column Configuration

```php
Tables\Columns\TextColumn::make('permissions.name')
    ->label('Direct Permissions')
    ->badge()
    ->color('success')
    ->searchable()
    ->toggleable()
    ->limit(3)
    ->tooltip(function (User $record): ?string {
        $permissions = $record->permissions->pluck('name')->toArray();
        return count($permissions) > 0 ? implode(', ', $permissions) : null;
    })
```

## Benefits

1. **Flexibility**: Grant specific permissions without creating new roles
2. **Granular Control**: Fine-tune access for individual users
3. **Temporary Access**: Easy to add/remove permissions as needed
4. **Audit Trail**: Clear visibility of who has what permissions
5. **No Role Pollution**: Avoid creating many single-use roles
6. **User-Friendly**: Grouped, searchable interface
7. **Visual Feedback**: See permissions in table view

## Best Practices

### When to Use Direct Permissions
✅ **Good Use Cases:**
- Temporary elevated access
- One-off special permissions
- Testing new permission combinations
- Cross-department collaboration
- Limited admin access

❌ **Avoid:**
- Assigning many permissions to many users (create a role instead)
- Using as primary permission mechanism
- Replacing role-based permissions entirely

### Permission Management Tips
1. **Document Why**: Keep notes on why specific permissions were granted
2. **Regular Review**: Periodically review and remove unnecessary permissions
3. **Principle of Least Privilege**: Only grant what's needed
4. **Use Roles First**: Assign appropriate role, then add extras if needed
5. **Test Access**: Verify permissions work as expected

## Security Considerations

1. **Permission Checking**: Always use `can()` method in code
2. **Combined Permissions**: User has access if they have permission from role OR direct assignment
3. **Revocation**: Removing direct permission doesn't affect role permissions
4. **Audit**: Track who assigns permissions to whom
5. **System Admin**: System Admins already have all permissions via role

## Testing Checklist

### Create User with Permissions
- [x] Create new user
- [x] Assign role
- [x] Expand "Additional Permissions" section
- [x] Select individual permissions
- [x] Save user
- [x] Verify permissions in table column

### Edit User Permissions
- [x] Open edit modal
- [x] Expand "Additional Permissions" section
- [x] Add new permissions
- [x] Remove existing permissions
- [x] Save changes
- [x] Verify changes in table

### Permission Functionality
- [x] User can access features granted by role
- [x] User can access features granted by direct permissions
- [x] User cannot access features not granted
- [x] Removing direct permission revokes access
- [x] Role change updates base permissions

### UI/UX
- [x] Section is collapsed by default
- [x] Permissions are grouped logically
- [x] Search works correctly
- [x] Bulk toggle works
- [x] Table column shows permissions
- [x] Tooltip shows all permissions
- [x] Modal width is comfortable

## Future Enhancements

Potential improvements:
1. **Permission Templates**: Save common permission sets
2. **Permission History**: Track permission changes over time
3. **Expiring Permissions**: Auto-revoke after specified date
4. **Permission Requests**: Users can request additional permissions
5. **Approval Workflow**: Require approval for sensitive permissions
6. **Permission Analytics**: Report on permission usage
7. **Smart Suggestions**: Suggest related permissions

## Status

✅ **Complete** - Individual permissions can now be assigned to all user types.
