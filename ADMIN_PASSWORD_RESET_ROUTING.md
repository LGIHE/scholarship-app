# Admin Password Reset Routing Implementation

## Problem Solved

System users (admins) were being sent to the regular applicant password reset flow (`/reset-password/...`) instead of the admin-specific flow (`/admin/password-reset/...`).

## Solution Implemented

### 1. Enabled Filament Password Reset

**File**: `app/Providers/Filament/AdminPanelProvider.php`
- Added `->passwordReset(\App\Filament\Pages\Auth\ResetPassword::class)` to enable admin password reset
- This creates the proper `/admin/password-reset/...` routes

### 2. Custom Admin Password Reset Page

**File**: `app/Filament/Pages/Auth/ResetPassword.php`
- Extends Filament's built-in `ResetPassword` class
- Automatically verifies email for system users during password reset
- Handles the password reset flow within the admin panel context

### 3. Updated Email URLs

**File**: `app/Mail/SystemUserCreated.php`
- **Before**: `url('/reset-password/' . $token . '?email=' . urlencode($user->email))`
- **After**: `url('/admin/password-reset/reset?token=' . $token . '&email=' . urlencode($user->email))`

### 4. Enhanced Email Template

**File**: `resources/views/emails/users/system-user-created.blade.php`
- Updated subject and content to clarify this is for admin access
- Button text changed to "Set Your Admin Password"
- Clear instructions about admin panel access

## Routing Flow

### System Users (Admins)
```
Email Link → /admin/password-reset/reset?token=...&email=...
           → Filament Admin Password Reset Page
           → Custom ResetPassword class handles verification
           → Redirect to /admin/login
           → User logs into admin panel
```

### Regular Users (Applicants)
```
Forgot Password → /forgot-password
                → /reset-password/token
                → Regular Laravel password reset
                → Redirect to /login
                → User logs into applicant portal
```

## Available Routes

### Admin Panel Routes
- `GET /admin/password-reset/request` - Request password reset (Filament)
- `GET /admin/password-reset/reset` - Reset password form (Filament)
- `POST /admin/password-reset/reset` - Process password reset (Filament)

### Applicant Portal Routes
- `GET /forgot-password` - Request password reset (Laravel)
- `POST /forgot-password` - Send reset email (Laravel)
- `GET /reset-password/{token}` - Reset password form (Laravel)
- `POST /reset-password` - Process password reset (Laravel)

## Technical Details

### URL Generation
```php
// System users get admin URLs
$adminResetUrl = url('/admin/password-reset/reset?token=' . $token . '&email=' . urlencode($email));

// Regular users get standard URLs  
$userResetUrl = url('/reset-password/' . $token . '?email=' . urlencode($email));
```

### Auto-Verification Logic
```php
// In App\Filament\Pages\Auth\ResetPassword
protected function handlePasswordReset($user, $password): void
{
    // Set password
    $user->forceFill(['password' => Hash::make($password)]);
    
    // Auto-verify email for system users
    if ($user->hasAnyRole(['System Admin', 'Committee Member'])) {
        $user->forceFill(['email_verified_at' => now()]);
    }
    
    $user->save();
}
```

## Benefits

### 1. Proper Separation
- ✅ Admin users use admin-themed password reset pages
- ✅ Applicants use applicant-themed password reset pages
- ✅ Clear visual distinction between the two flows

### 2. Better UX
- ✅ Admin users stay within the admin panel context
- ✅ Consistent branding and styling
- ✅ Appropriate redirects after password reset

### 3. Security
- ✅ Auto-verification for admin users (they don't need email verification)
- ✅ Proper role-based handling
- ✅ Secure token-based password reset

### 4. Maintainability
- ✅ Uses Filament's built-in functionality
- ✅ Follows Laravel/Filament conventions
- ✅ Easy to customize further if needed

## Testing

### Test Admin Password Reset Flow

1. **Create System User** (leave password blank):
   ```
   Admin Panel → System Users → Create
   Name: Test Admin
   Email: admin@test.com
   Role: System Admin
   Password: [leave blank]
   ```

2. **Check Email**:
   - Subject: "Welcome to LGF Admin System - Set Your Password"
   - Button: "Set Your Admin Password"
   - URL should contain: `/admin/password-reset/reset?token=...`

3. **Click Setup Link**:
   - Should go to admin-themed password reset page
   - URL: `yoursite.com/admin/password-reset/reset?token=...`
   - Page should have admin panel styling

4. **Set Password**:
   - Enter new password (twice)
   - Submit form
   - Should redirect to `/admin/login`

5. **Login**:
   - Use email and new password
   - Should successfully access admin panel

### Test Applicant Password Reset Flow

1. **Go to Applicant Login**: `yoursite.com/login`
2. **Click "Forgot Password"**: Should go to `/forgot-password`
3. **Enter Email**: Should send reset email
4. **Check Email**: URL should contain `/reset-password/token`
5. **Click Reset Link**: Should go to applicant-themed reset page
6. **Set Password**: Should redirect to `/login` (applicant portal)

## Verification Commands

```bash
# Check routes are available
php artisan route:list | grep -E "(admin.*password|password)"

# Test URL generation
php artisan tinker
>>> $user = User::where('email', 'admin@test.com')->first();
>>> $token = Password::createToken($user);
>>> url('/admin/password-reset/reset?token=' . $token . '&email=' . urlencode($user->email));
```

## Files Changed

### New Files
- `app/Filament/Pages/Auth/ResetPassword.php` - Custom admin password reset page

### Modified Files
- `app/Providers/Filament/AdminPanelProvider.php` - Enabled password reset with custom page
- `app/Mail/SystemUserCreated.php` - Updated to use admin reset URLs
- `resources/views/emails/users/system-user-created.blade.php` - Enhanced for admin context

### Removed Files
- `app/Http/Controllers/Auth/SystemUserPasswordResetController.php` - No longer needed

## Future Enhancements

### Possible Improvements
1. **Custom Admin Reset Email**: Create admin-specific password reset request emails
2. **Branding**: Customize admin reset pages with organization branding
3. **Multi-Panel Support**: If adding more Filament panels, ensure proper routing
4. **Rate Limiting**: Add specific rate limits for admin password resets
5. **Audit Logging**: Log admin password reset activities

## Troubleshooting

### Common Issues

**Admin reset link goes to wrong page?**
- Check that Filament password reset is enabled in AdminPanelProvider
- Verify custom ResetPassword page is registered
- Clear route cache: `php artisan route:clear`

**Email contains wrong URL?**
- Check SystemUserCreated mail class uses correct URL format
- Verify APP_URL is set correctly in .env

**Password reset doesn't auto-verify email?**
- Check custom ResetPassword page has the auto-verification logic
- Verify user has correct roles (System Admin, Committee Member)

**Styling looks wrong?**
- Ensure using Filament's ResetPassword as base class
- Check that admin panel theme is properly configured

### Debug Commands

```bash
# Check Filament routes
php artisan route:list | grep filament

# Test email URL generation
php artisan tinker
>>> app(App\Mail\SystemUserCreated::class, [User::first()])->setupUrl

# Check user roles
>>> User::find(1)->getRoleNames()
```

## Security Considerations

1. **Token Security**: Uses Laravel's secure password reset tokens
2. **Time Limits**: Tokens expire automatically
3. **Role Verification**: Only users with admin roles get admin URLs
4. **Auto-Verification**: Admin users don't need separate email verification
5. **HTTPS**: Ensure production uses HTTPS for all password reset links

This implementation provides a clean separation between admin and applicant password reset flows while maintaining security and usability.