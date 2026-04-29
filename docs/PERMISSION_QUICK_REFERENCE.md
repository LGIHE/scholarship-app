# Permission Quick Reference Card

## Quick Permission Matrix

| Permission | What It Controls | Who Typically Has It |
|------------|------------------|---------------------|
| **Applications** | | |
| `application.view_any` | See Applications menu | System Admin, Committee Member, Reviewers |
| `application.view` | View individual applications | System Admin, Committee Member, Reviewers |
| `application.create` | Create new applications | System Admin |
| `application.edit` | Edit applications | System Admin |
| `application.delete` | Delete applications | System Admin |
| `application.review` | Mark as "Under Review" | System Admin, Committee Member, Reviewers |
| `application.approve` | Approve applications | System Admin, Committee Member |
| `application.reject` | Reject applications | System Admin, Committee Member |
| `application.export` | Export application data | System Admin, Committee Member |
| **Scholars** | | |
| `scholar.view_any` | See Scholars menu | System Admin, Committee Member, Coordinators |
| `scholar.view` | View individual scholars | System Admin, Committee Member, Coordinators |
| `scholar.create` | Create scholar records | System Admin |
| `scholar.edit` | Edit scholar information | System Admin |
| `scholar.delete` | Delete scholar records | System Admin |
| `scholar.view_bio` | View Bio tab | System Admin, Committee Member, Coordinators |
| `scholar.view_applications` | View Applications tab | System Admin, Committee Member, Coordinators |
| `scholar.view_progress` | View Progress tab | System Admin, Committee Member, Coordinators |
| `scholar.view_documents` | View Documents tab | System Admin, Committee Member, Coordinators |
| `scholar.edit_progress` | Edit academic progress | System Admin, Coordinators |
| `scholar.upload_documents` | Upload documents | System Admin, Coordinators |
| `scholar.export` | Export scholar data | System Admin |
| **Users** | | |
| `user.view_any` | See all user lists | System Admin |
| `user.view` | View individual users | System Admin |
| `user.create` | Create new users | System Admin |
| `user.edit` | Edit users | System Admin |
| `user.delete` | Delete users | System Admin |
| `user.manage_applicants` | See Applicants menu | System Admin |
| `user.manage_system_users` | See System Users menu | System Admin |
| `user.export` | Export user data | System Admin |
| **Roles** | | |
| `role.view` | See Roles menu & view roles | System Admin |
| `role.create` | Create new roles | System Admin |
| `role.edit` | Edit roles | System Admin |
| `role.delete` | Delete roles | System Admin |
| **Dashboard** | | |
| `dashboard.view` | Access dashboard | Everyone with admin access |
| `dashboard.view_stats` | See statistics widgets | System Admin, Committee Member |
| `dashboard.view_charts` | See charts | System Admin, Committee Member |
| **Reports** | | |
| `report.view` | View reports | System Admin, Committee Member |
| `report.generate` | Generate new reports | System Admin, Committee Member |
| `report.export` | Export reports | System Admin |
| **Settings** | | |
| `settings.view` | View system settings | System Admin |
| `settings.edit` | Edit system settings | System Admin |
| `settings.manage_email` | Manage email settings | System Admin |
| `settings.manage_notifications` | Manage notifications | System Admin |

## Common Role Templates

### 🔴 System Admin (Full Access)
**All 47 permissions** - Complete control over the system

**Sees:**
- Dashboard (all widgets)
- Applications (all actions)
- Applicants (all actions)
- Scholars (all actions)
- System Users (all actions)
- Roles (all actions)

---

### 🟡 Committee Member (Review & Approve)
**15 permissions** - Can review and approve applications, view scholars

**Permissions:**
- `application.view`, `application.view_any`, `application.review`, `application.approve`, `application.reject`, `application.export`
- `scholar.view`, `scholar.view_any`, `scholar.view_bio`, `scholar.view_applications`, `scholar.view_progress`, `scholar.view_documents`
- `dashboard.view`, `dashboard.view_stats`, `dashboard.view_charts`
- `report.view`, `report.generate`

**Sees:**
- Dashboard (all widgets)
- Applications (View, Review, Approve, Reject)
- Scholars (View only, all tabs)

**Cannot:**
- Create, edit, or delete anything
- Manage users or roles
- Change system settings

---

### 🔵 Application Reviewer (Review Only)
**5 permissions** - Can only view and review applications

**Permissions:**
- `application.view`
- `application.view_any`
- `application.review`
- `dashboard.view`
- `dashboard.view_stats`

**Sees:**
- Dashboard (stats widgets only)
- Applications (View, Mark as Under Review)

**Cannot:**
- Approve or reject applications
- Access scholars
- Manage users or roles

---

### 🟢 Scholar Coordinator (Scholar Management)
**10 permissions** - Manages scholar progress and documents

**Permissions:**
- `scholar.view`, `scholar.view_any`
- `scholar.view_bio`, `scholar.view_applications`, `scholar.view_progress`, `scholar.view_documents`
- `scholar.edit_progress`, `scholar.upload_documents`
- `dashboard.view`
- `report.view`, `report.generate`

**Sees:**
- Dashboard (basic view)
- Scholars (View, all tabs, can edit progress and upload documents)

**Cannot:**
- Access applications
- Edit scholar bio information
- Manage users or roles

---

### 🟣 Read-Only Auditor
**12 permissions** - Can view everything but not make changes

**Permissions:**
- `application.view`, `application.view_any`
- `scholar.view`, `scholar.view_any`, `scholar.view_bio`, `scholar.view_applications`, `scholar.view_progress`, `scholar.view_documents`
- `user.view`, `user.view_any`
- `dashboard.view`, `dashboard.view_stats`

**Sees:**
- Dashboard (stats widgets)
- Applications (View only)
- Scholars (View only, all tabs)

**Cannot:**
- Create, edit, or delete anything
- Approve or reject applications
- Manage users or roles

---

## Permission Hierarchy

### View Permissions (Required First)
Always grant these before granting edit/delete:
1. `resource.view_any` (to see the menu)
2. `resource.view` (to view individual records)
3. Then grant `resource.edit`, `resource.delete`, etc.

### Example Correct Order:
✅ Grant `application.view_any` → User sees Applications menu
✅ Grant `application.view` → User can click and view applications
✅ Grant `application.edit` → User can now edit applications

### Example Wrong Order:
❌ Grant only `application.edit` → User doesn't see Applications menu (missing `view_any`)

## Quick Commands

### Clear Permission Cache
```bash
php artisan permission:cache-reset
php artisan optimize:clear
```

### Create Demo Roles
```bash
php artisan db:seed --class=PermissionDemoSeeder
```

### Check User Permissions
```bash
php artisan tinker
>>> $user = User::find(1);
>>> $user->getAllPermissions()->pluck('name');
>>> $user->roles->pluck('name');
```

### Assign Permission to Role
```bash
php artisan tinker
>>> $role = Role::findByName('Committee Member');
>>> $role->givePermissionTo('application.approve');
```

### Remove Permission from Role
```bash
php artisan tinker
>>> $role = Role::findByName('Committee Member');
>>> $role->revokePermissionTo('application.delete');
```

## Navigation Visibility Rules

| If User Has... | They See... |
|----------------|-------------|
| `application.view_any` | Applications menu item |
| `scholar.view_any` | Scholars menu item |
| `user.manage_applicants` | Applicants menu item |
| `user.manage_system_users` | System Users menu item |
| `role.view` | Roles menu item |
| `dashboard.view` | Dashboard access |
| **None of the above** | Empty sidebar |

## Action Button Visibility

| Button | Required Permission |
|--------|-------------------|
| View (👁) | `resource.view` |
| Edit (✏️) | `resource.edit` |
| Delete (🗑️) | `resource.delete` |
| Under Review | `application.review` |
| Approve (✅) | `application.approve` |
| Reject (❌) | `application.reject` |

## Troubleshooting Checklist

- [ ] User has the role assigned?
- [ ] Role has the required permissions?
- [ ] Permission cache cleared?
- [ ] Browser cache cleared?
- [ ] User logged out and back in?
- [ ] Permission name spelled correctly?
- [ ] View permissions granted before edit permissions?

## Common Issues

### "I granted edit permission but user can't see the menu"
**Solution:** Also grant `resource.view_any` permission

### "User can see menu but gets access denied"
**Solution:** Grant `resource.view` permission

### "Changes not taking effect"
**Solution:** Run `php artisan permission:cache-reset`

### "User sees buttons but gets error when clicking"
**Solution:** Grant the specific action permission (e.g., `application.approve`)

## Best Practices

1. ✅ Always grant view permissions first
2. ✅ Test new roles with a test user account
3. ✅ Document custom roles and their purposes
4. ✅ Review permissions quarterly
5. ✅ Use descriptive role names
6. ✅ Clear cache after permission changes
7. ✅ Grant minimum necessary permissions
8. ✅ Use role templates as starting points

## Emergency Access

If you're locked out:

```bash
# Grant all permissions to your user via tinker
php artisan tinker
>>> $user = User::where('email', 'your@email.com')->first();
>>> $user->assignRole('System Admin');
>>> exit
php artisan permission:cache-reset
```

---

**Last Updated:** April 28, 2026
**Total Permissions:** 47
**Default Roles:** 4 (System Admin, Committee Member, Scholar, Applicant)
