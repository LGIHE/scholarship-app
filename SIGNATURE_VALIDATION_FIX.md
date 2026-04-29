# Signature Validation Fix for Admin Password Reset

## Problem

Users were getting "403 Invalid signature" error when clicking the password reset link in their email. This happened because:

1. Filament's password reset routes require signed URLs (`ValidateSignature` middleware)
2. Signed URLs depend on `APP_KEY` and `APP_URL` being identical between generation and validation
3. Production environment differences caused signature validation to fail

## Root Cause

```bash
# Filament's password reset route has ValidateSignature middleware
GET admin/password-reset/reset filament.admin.auth.password-reset.reset
⇂ Illuminate\Routing\Middleware\ValidateSignature
```

When the email was generated in one environment (or with different `APP_KEY`/`APP_URL`) and validated in another, the signature became invalid.

## Solution Implemented

### 1. Custom Redirect Route

**File**: `app/Http/Controllers/Auth/AdminPasswordSetupController.php`

Created a new route that:
- ✅ Accepts unsigned URLs from emails
- ✅ Validates the password reset token
- ✅ Verifies user permissions
- ✅ Creates a fresh signed URL
- ✅ Redirects to Filament's password reset page

### 2. Updated Email URLs

**File**: `app/Mail/SystemUserCreated.php`

- **Before**: Direct link to `filament.admin.auth.password-reset.reset` (required signature)
- **After**: Link to `admin.password.setup` (no signature required)

### 3. New Route Registration

**File**: `routes/auth.php`

```php
Route::get('admin-password-setup', [AdminPasswordSetupController::class, 'setup'])
    ->name('admin.password.setup');
```

## Flow Diagram

### New Flow (Fixed)
```
Email Link → /admin-password-setup?token=...&email=...
           → AdminPasswordSetupController validates token
           → Creates fresh signed URL
           → Redirects to /admin/password-reset/reset?signature=...
           → Filament password reset page
           → User sets password
           → Redirect to admin login
```

### Old Flow (Broken)
```
Email Link → /admin/password-reset/reset?token=...&signature=...
           → ValidateSignature middleware fails
           → 403 Invalid signature error
```

## Technical Details

### AdminPasswordSetupController Logic

```php
public function setup(Request $request)
{
    // 1. Validate parameters
    $request->validate(['token' => 'required', 'email' => 'required|email']);
    
    // 2. Verify token is valid
    $user = User::where('email', $request->email)->first();
    if (!$user || !Password::tokenExists($user, $request->token)) {
        return redirect()->route('filament.admin.auth.login')
            ->withErrors(['email' => 'Invalid or expired token']);
    }
    
    // 3. Verify user permissions
    if (!$user->hasAnyRole(['System Admin', 'Committee Member'])) {
        return redirect()->route('login')
            ->withErrors(['email' => 'Admin access only']);
    }
    
    // 4. Create fresh signed URL
    $signedUrl = URL::temporarySignedRoute(
        'filament.admin.auth.password-reset.reset',
        now()->addMinutes(30),
        ['token' => $request->token, 'email' => $request->email]
    );
    
    // 5. Redirect to Filament
    return redirect($signedUrl);
}
```

### Security Features

1. **Token Validation**: Verifies password reset token is valid
2. **User Verification**: Ensures user exists and has admin roles
3. **Fresh Signatures**: Creates new signed URL with current environment
4. **Time Limits**: Signed URLs expire in 30 minutes
5. **Error Handling**: Proper redirects for invalid requests

## Benefits

### 1. Reliability
- ✅ Works across different environments
- ✅ No dependency on consistent `APP_KEY`/`APP_URL`
- ✅ Handles production deployment scenarios

### 2. Security
- ✅ Still uses Filament's secure password reset system
- ✅ Validates tokens before creating signed URLs
- ✅ Verifies user permissions
- ✅ Time-limited access

### 3. User Experience
- ✅ No more "403 Invalid signature" errors
- ✅ Seamless redirect to password reset page
- ✅ Clear error messages for invalid links
- ✅ Maintains admin panel context

### 4. Maintainability
- ✅ Uses standard Laravel/Filament patterns
- ✅ Easy to debug and troubleshoot
- ✅ Clear separation of concerns

## Testing

### Test Valid Password Reset

1. **Create System User** (no password):
   ```
   Admin Panel → System Users → Create
   Email: admin@test.com
   Leave password blank
   ```

2. **Check Email URL**:
   ```
   Should contain: /admin-password-setup?token=...&email=...
   Should NOT contain: signature=
   ```

3. **Click Link**:
   - Should redirect to admin password reset page
   - URL should change to include signature
   - Should show password reset form

4. **Set Password**:
   - Enter new password
   - Should redirect to admin login
   - Should be able to log in

### Test Error Scenarios

**Invalid Token**:
```
/admin-password-setup?token=invalid&email=test@example.com
→ Should redirect to admin login with error
```

**Non-Admin User**:
```
/admin-password-setup?token=valid&email=applicant@example.com  
→ Should redirect to regular login with error
```

**Missing Parameters**:
```
/admin-password-setup
→ Should show validation errors
```

## Troubleshooting

### Common Issues

**Still getting signature errors?**
- Check that route is registered: `php artisan route:list | grep admin-password-setup`
- Verify controller exists and is correct
- Clear route cache: `php artisan route:clear`

**Redirect not working?**
- Check logs for controller errors
- Verify user has correct roles
- Ensure password reset token is valid

**Email contains wrong URL?**
- Check SystemUserCreated mail class
- Verify route name is correct: `admin.password.setup`
- Test URL generation in tinker

### Debug Commands

```bash
# Test route exists
php artisan route:list | grep admin-password-setup

# Test URL generation
php artisan tinker
>>> route('admin.password.setup', ['token' => 'test', 'email' => 'test@example.com'])

# Test token validation
>>> $user = User::where('email', 'admin@test.com')->first();
>>> Password::tokenExists($user, 'token-from-email');
```

## Files Changed

### New Files
- `app/Http/Controllers/Auth/AdminPasswordSetupController.php` - Handles redirect logic

### Modified Files
- `routes/auth.php` - Added new route
- `app/Mail/SystemUserCreated.php` - Updated to use new route
- `app/Filament/Resources/SystemUserResource/Pages/ListSystemUsers.php` - Reverted to custom mail

### Removed Files
- `app/Http/Middleware/BypassSignatureForPasswordReset.php` - No longer needed

## Production Deployment

### Deployment Steps

1. **Deploy Code**: Push changes to production
2. **Clear Caches**: 
   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan cache:clear
   ```
3. **Test Email**: Create test user and verify email works
4. **Monitor Logs**: Check for any errors in password reset flow

### Environment Considerations

- ✅ No special environment variables required
- ✅ Works with any `APP_KEY` and `APP_URL` configuration
- ✅ Compatible with load balancers and multiple servers
- ✅ Handles environment differences gracefully

## Future Enhancements

### Possible Improvements

1. **Rate Limiting**: Add rate limiting to setup route
2. **Audit Logging**: Log password reset attempts
3. **Custom Styling**: Customize the redirect page
4. **Multi-Panel Support**: Handle multiple Filament panels
5. **Token Cleanup**: Clean up expired tokens

### Monitoring

```bash
# Monitor password reset usage
tail -f storage/logs/laravel.log | grep "AdminPasswordSetup"

# Check for signature errors (should be none now)
grep -i "invalid signature" storage/logs/laravel.log
```

This solution provides a robust, secure, and user-friendly password reset experience for admin users while avoiding signature validation issues.