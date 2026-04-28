# Role Assignment Fix

## Problem

When creating or editing users, the following error occurred:

```
Illuminate\Database\QueryException
SQLSTATE[23000]: Integrity constraint violation: 19 FOREIGN KEY constraint failed 
(Connection: sqlite, SQL: insert into "model_has_roles" ("model_id", "model_type", "role_id") 
values (3, App\Models\User, Committee Member))
```

## Root Cause

The Select field for role assignment was configured incorrectly:

```php
// WRONG - Uses role name as the key instead of ID
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->options([
        'System Admin' => 'System Admin',  // Key should be ID, not name
        'Committee Member' => 'Committee Member',
    ])
```

This caused Filament to try to insert the role **name** (e.g., "Committee Member") as the `role_id` in the `model_has_roles` table, instead of the numeric ID (e.g., 4).

## Solution

Changed the options to dynamically fetch role IDs from the database:

```php
// CORRECT - Uses role ID as the key
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->options(function () {
        return \Spatie\Permission\Models\Role::whereIn('name', ['System Admin', 'Committee Member'])
            ->pluck('name', 'id');  // ID as key, name as value
    })
```

## Files Modified

### 1. ApplicantUserResource.php
**Before:**
```php
->options([
    'Applicant' => 'Applicant',
])
->default('Applicant')
```

**After:**
```php
->options(function () {
    return \Spatie\Permission\Models\Role::where('name', 'Applicant')
        ->pluck('name', 'id');
})
->default(function () {
    return \Spatie\Permission\Models\Role::where('name', 'Applicant')->first()?->id;
})
```

### 2. SystemUserResource.php
**Before:**
```php
->options([
    'System Admin' => 'System Admin',
    'Committee Member' => 'Committee Member',
])
```

**After:**
```php
->options(function () {
    return \Spatie\Permission\Models\Role::whereIn('name', ['System Admin', 'Committee Member'])
        ->pluck('name', 'id');
})
```

### 3. ScholarUserResource.php
No changes needed - this resource doesn't have a role selection field in the form.

## How It Works

### pluck('name', 'id')
This Laravel collection method creates an array where:
- **Key** = Role ID (numeric, e.g., 1, 2, 3, 4)
- **Value** = Role Name (string, e.g., "System Admin", "Committee Member")

Example output:
```php
[
    1 => 'System Admin',
    4 => 'Committee Member',
]
```

### Why This Fixes the Issue

When the form is submitted:
1. User selects "Committee Member" from dropdown
2. Filament gets the selected **key** (which is now the role ID: 4)
3. Inserts into `model_has_roles` table: `role_id = 4` ✅
4. Foreign key constraint is satisfied because role ID 4 exists in `roles` table

## Current Role IDs

From the database:
- **ID 1**: System Admin
- **ID 2**: Applicant
- **ID 3**: Scholar
- **ID 4**: Committee Member

## Testing

### Test Creating System User
1. Go to System Users
2. Click "New System User"
3. Fill in name, email, password
4. Select "Committee Member" role
5. Click "Create"
6. ✅ User should be created successfully

### Test Creating Applicant
1. Go to Applicants
2. Click "New Applicant"
3. Fill in name, email, password
4. Role should default to "Applicant"
5. Click "Create"
6. ✅ User should be created successfully

### Test Editing User
1. Click edit on any user
2. Change role (if applicable)
3. Click "Save"
4. ✅ User should be updated successfully

## Benefits

1. **Correct Foreign Key**: Uses numeric IDs instead of strings
2. **Dynamic**: Automatically adapts if role IDs change
3. **Database-Driven**: Always uses actual role data from database
4. **Type-Safe**: Maintains proper data types for foreign keys

## Prevention

To avoid this issue in the future:
- Always use `pluck('display_field', 'id_field')` for relationship selects
- Never use string values as keys when the database expects numeric IDs
- Test create/edit operations after modifying relationship fields

## Status

✅ **Fixed** - Role assignment now works correctly for all user resources.
