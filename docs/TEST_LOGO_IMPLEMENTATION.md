# Logo Implementation Testing Guide

## Quick Test Commands

### 1. Clear Caches
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### 2. Rebuild Frontend Assets (if using Vite)
```bash
npm run build
# or for development
npm run dev
```

### 3. Start Development Server
```bash
php artisan serve
```

---

## Manual Testing Checklist

### ✅ Public Pages (Not Logged In)

#### Homepage (/)
- [ ] Logo appears in header (top left)
- [ ] Logo is 48px height, maintains aspect ratio
- [ ] Logo is clickable and links to homepage
- [ ] White logo appears in footer
- [ ] Footer logo is visible against dark background
- [ ] Both logos load without errors (check browser console)

#### Navigation Test
- [ ] Visit `/about` - header logo present
- [ ] Visit `/resources` - header logo present
- [ ] Visit `/faq` - header logo present
- [ ] Visit `/contact` - header logo present
- [ ] All pages show footer with white logo

### ✅ Authentication Pages

#### Login Page (/login)
- [ ] Logo appears centered above login form
- [ ] Logo is 80px height (larger than header)
- [ ] Logo is clickable and links to homepage
- [ ] Logo loads correctly

#### Register Page (/register)
- [ ] Logo appears centered above registration form
- [ ] Logo is 80px height
- [ ] Logo is clickable and links to homepage

#### Password Reset
- [ ] Logo appears on forgot password page
- [ ] Logo appears on reset password page

### ✅ Authenticated User Area

#### Dashboard (/portal)
- [ ] Logo appears in top navigation bar
- [ ] Logo is 40px height
- [ ] Logo is clickable and links to homepage
- [ ] Logo visible on all authenticated pages

#### Application Form
- [ ] Logo present in navigation
- [ ] Logo maintains consistent size

#### Profile Page
- [ ] Logo present in navigation

### ✅ Admin Panel (/admin)

#### Admin Login
- [ ] Logo appears on admin login page
- [ ] Logo is properly sized

#### Admin Dashboard
- [ ] Logo appears in sidebar
- [ ] Logo is 48px height (3rem)
- [ ] Logo is visible and clear
- [ ] Favicon shows in browser tab

#### Admin Resources
- [ ] Logo visible on Applications page
- [ ] Logo visible on Scholars page
- [ ] Logo visible on Users page
- [ ] Logo visible on Roles page

### ✅ Email Templates

#### Send Test Emails
```bash
# Trigger application received email (requires test data)
php artisan tinker
>>> $user = User::first();
>>> $application = Application::first();
>>> Mail::to($user->email)->send(new \App\Mail\ApplicationReceived($application));
```

#### Email Checklist
- [ ] Logo appears in email header
- [ ] Logo is centered
- [ ] Logo size is appropriate (not too large)
- [ ] Logo loads in email preview
- [ ] Test in multiple email clients:
  - [ ] Gmail (web)
  - [ ] Outlook (web)
  - [ ] Apple Mail
  - [ ] Mobile email app

### ✅ Browser Compatibility

Test in multiple browsers:
- [ ] Chrome/Edge
- [ ] Firefox
- [ ] Safari
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

### ✅ Responsive Design

Test at different screen sizes:
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

Check that:
- [ ] Logos scale appropriately
- [ ] Logos don't overflow containers
- [ ] Logos maintain aspect ratio
- [ ] White logo remains visible on dark backgrounds

### ✅ Performance

- [ ] Logo files load quickly (check Network tab)
- [ ] No 404 errors for logo files
- [ ] Images are optimized (check file sizes)
  - logo.png: ~29KB ✓
  - logo-white.png: ~62KB ✓

---

## Common Issues & Solutions

### Issue: Logo not appearing
**Solution:**
```bash
# Clear all caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Rebuild assets
npm run build
```

### Issue: Logo appears broken/distorted
**Solution:**
- Check that logo files exist in `public/images/`
- Verify file permissions: `chmod 644 public/images/logo*.png`
- Check browser console for errors

### Issue: White logo not visible in footer
**Solution:**
- Verify `logo-white.png` exists
- Check footer background color is dark
- Inspect element to ensure correct image path

### Issue: Admin panel logo not showing
**Solution:**
```bash
# Clear Filament cache
php artisan filament:cache-components
php artisan cache:clear
```

### Issue: Email logo not appearing
**Solution:**
- Ensure mail views are published
- Check `resources/views/vendor/mail/html/header.blade.php`
- Verify logo path uses `asset()` helper
- Test with actual email send (not just preview)

### Issue: Favicon not updating
**Solution:**
- Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)
- Clear browser cache
- Check `AdminPanelProvider.php` has `->favicon()` configured

---

## Automated Testing (Optional)

### Browser Testing with Dusk
```php
// tests/Browser/LogoTest.php
public function testLogoAppearsOnHomepage()
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertVisible('img[alt="LGF Scholarship Logo"]')
                ->assertAttribute('img[alt="LGF Scholarship Logo"]', 'src', '/images/logo.png');
    });
}
```

### Visual Regression Testing
Consider using tools like:
- Percy.io
- Chromatic
- BackstopJS

---

## Sign-off Checklist

Before marking as complete:
- [ ] All public pages tested
- [ ] All auth pages tested
- [ ] Admin panel tested
- [ ] Email templates tested
- [ ] Mobile responsive tested
- [ ] Multiple browsers tested
- [ ] No console errors
- [ ] Documentation reviewed
- [ ] Screenshots taken (optional)

---

## Screenshots Location

Save test screenshots to:
```
docs/screenshots/
├── homepage-header.png
├── homepage-footer.png
├── login-page.png
├── dashboard-nav.png
├── admin-sidebar.png
└── email-template.png
```

---

## Reporting Issues

If you find any issues:
1. Note the page/location
2. Take a screenshot
3. Check browser console for errors
4. Document steps to reproduce
5. Check if issue persists after cache clear

---

## Success Criteria

✅ Logo implementation is successful when:
1. All logos display correctly across all pages
2. No broken images or 404 errors
3. Logos are appropriately sized for each context
4. White logo is visible on dark backgrounds
5. Logos are clickable where appropriate
6. Email templates show logo correctly
7. Admin panel displays logo and favicon
8. Responsive design works on all devices
9. No performance issues
10. All documentation is accurate
