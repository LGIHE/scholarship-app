# Individual Permissions - Quick Summary

## What's New?

You can now assign **individual permissions** to users beyond their role-based permissions!

## How It Works

### Permission Model
- **Role Permissions** (Base) + **Direct Permissions** (Extra) = **Total Access**

### Example
- User: Jane Doe
- Role: Committee Member (has application review permissions)
- Direct Permissions: user.create, user.edit (added individually)
- Result: Jane can review applications AND manage users

## Using the Feature

### Creating/Editing a User

1. **Open user form** (create new or edit existing)
2. **Select role** (determines base permissions)
3. **Expand "Additional Permissions"** section (collapsed by default)
4. **Check permissions** you want to grant
   - Grouped by category (application, scholar, user, etc.)
   - Searchable
   - Bulk select/deselect available
5. **Save** - Done!

### Viewing Permissions

**In Table:**
- New "Direct Permissions" column shows assigned permissions
- Hover over badges to see all permissions
- Column is toggleable

**In Form:**
- Expand "Additional Permissions" section
- Checked boxes show current assignments

## Key Features

✅ **Grouped by Category** - Easy to find related permissions
✅ **Searchable** - Quickly find specific permissions
✅ **Bulk Toggle** - Select/deselect all at once
✅ **Collapsible** - Keeps form clean when not needed
✅ **Visual Feedback** - See permissions in table view
✅ **Larger Modal** - Increased to 4xl for comfortable viewing

## Common Use Cases

### 1. Temporary Access
Grant a Committee Member temporary user management access during peak season.

### 2. Limited Admin
Give someone settings access without full System Admin role.

### 3. Cross-Department
Allow an Applicant to view scholar information for mentoring.

### 4. Audit Access
Grant read-only access across all modules for auditors.

## Permission Categories

- **application** - Application management
- **scholar** - Scholar management
- **user** - User management
- **role** - Role management
- **permission** - Permission management
- **dashboard** - Dashboard access
- **report** - Report generation
- **settings** - System settings

## Files Modified

**Resources (3):**
- ApplicantUserResource.php
- ScholarUserResource.php
- SystemUserResource.php

**List Pages (3):**
- ListApplicantUsers.php
- ListScholarUsers.php
- ListSystemUsers.php

## Best Practices

✅ **Do:**
- Use for temporary or special access
- Grant only what's needed
- Review permissions regularly
- Document why permissions were granted

❌ **Don't:**
- Assign many permissions to many users (create a role instead)
- Use as primary permission mechanism
- Grant permissions "just in case"

## Technical Notes

- Uses Spatie Laravel Permission package
- Permissions are additive (role + direct)
- Removing direct permission doesn't affect role permissions
- System Admins already have all permissions via role

## Status

✅ **Ready to Use** - Feature is fully implemented and tested!

---

**Full Documentation:** See `docs/INDIVIDUAL_PERMISSIONS_IMPLEMENTATION.md`
