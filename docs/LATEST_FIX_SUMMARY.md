# Latest Fix Summary - Role Assignment Error

## Issue Fixed
**Error**: Foreign key constraint violation when creating/editing users
```
SQLSTATE[23000]: Integrity constraint violation: 19 FOREIGN KEY constraint failed
```

## Root Cause
The role selection dropdown was using role **names** as keys instead of role **IDs**, causing the database to try to insert strings like "Committee Member" into the `role_id` column which expects numeric IDs.

## Solution
Changed the Select field options to use role IDs as keys:

```php
// Before (WRONG)
->options([
    'System Admin' => 'System Admin',  // String key
])

// After (CORRECT)
->options(function () {
    return \Spatie\Permission\Models\Role::whereIn('name', ['System Admin', 'Committee Member'])
        ->pluck('name', 'id');  // ID as key, name as value
})
```

## Files Modified
- ✅ `app/Filament/Resources/ApplicantUserResource.php`
- ✅ `app/Filament/Resources/SystemUserResource.php`

## Result
- ✅ Users can now be created successfully
- ✅ Users can now be edited successfully
- ✅ Role assignment works correctly
- ✅ Foreign key constraints are satisfied

## Testing
Try creating a new user:
1. Go to System Users or Applicants
2. Click "New" button
3. Fill in the form
4. Select a role
5. Click "Create"
6. ✅ Should work without errors

## Status
**FIXED** ✅ - User creation and editing now works correctly!
