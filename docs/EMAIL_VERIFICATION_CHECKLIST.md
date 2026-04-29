# Email Implementation Verification Checklist

Use this checklist to verify that all email functionality is working correctly.

## ✅ Pre-Flight Checks

### Environment Setup
- [ ] `MAIL_MAILER=resend` is set in `.env`
- [ ] `RESEND_API_KEY` is set in `.env`
- [ ] `MAIL_FROM_ADDRESS` is set in `.env`
- [ ] `MAIL_FROM_NAME` is set in `.env`
- [ ] Queue worker is running: `php artisan queue:work`
- [ ] Application is running: `php artisan serve`

### Package Installation
- [ ] Resend package installed: `composer show resend/resend-php`
- [ ] No composer errors

### File Existence
- [ ] `app/Mail/ApplicationReceived.php` exists
- [ ] `app/Mail/ApplicationApproved.php` exists
- [ ] `app/Mail/ApplicationStatusUpdated.php` exists
- [ ] `app/Mail/ApplicationRejected.php` exists
- [ ] `app/Mail/SystemUserCreated.php` exists
- [ ] `app/Mail/WelcomeApplicant.php` exists
- [ ] `app/Listeners/SendWelcomeEmail.php` exists

## 🧪 Functional Testing

### 1. User Registration & Email Verification

**Test Steps:**
1. Navigate to registration page
2. Register a new user with valid email
3. Check email inbox

**Expected Results:**
- [ ] Welcome email received within 1 minute
- [ ] Email subject: "Welcome to Luigi Giussani Foundation - Verify Your Email"
- [ ] Email contains user's name
- [ ] Email contains "Verify Email Address" button
- [ ] Clicking button verifies email successfully
- [ ] User can log in after verification

**Troubleshooting:**
```bash
# Check queue jobs
php artisan queue:failed

# Check logs
tail -f storage/logs/laravel.log | grep -i "welcome"
```

---

### 2. Application Submission Confirmation

**Test Steps:**
1. Log in as applicant
2. Complete and submit scholarship application
3. Check email inbox

**Expected Results:**
- [ ] Confirmation email received within 1 minute
- [ ] Email subject: "We have received your LGF Scholarship Application"
- [ ] Email contains applicant's name
- [ ] Email contains "View Application Status" button
- [ ] Button links to portal

**Troubleshooting:**
```bash
# Check logs
tail -f storage/logs/laravel.log | grep -i "application"
```

---

### 3. Application Status: Under Review

**Test Steps:**
1. Log in to Filament admin panel
2. Navigate to Applications
3. Click "Under Review" action on a submitted application
4. Confirm the action
5. Check applicant's email inbox

**Expected Results:**
- [ ] Status update email received within 1 minute
- [ ] Email subject: "LGF Scholarship Application Status Update"
- [ ] Email shows old status: "Submitted"
- [ ] Email shows new status: "Under Review"
- [ ] Email contains "View Application Status" button

**Troubleshooting:**
```bash
# Check logs
tail -f storage/logs/laravel.log | grep -i "status update"
```

---

### 4. Application Approval

**Test Steps:**
1. Log in to Filament admin panel
2. Navigate to Applications
3. Click "Approve" action on an application
4. Confirm the action
5. Check applicant's email inbox

**Expected Results:**
- [ ] Approval email received within 1 minute
- [ ] Email subject: "Congratulations - LGF Scholarship Application Approved!"
- [ ] Email contains applicant's name
- [ ] Email contains "Go to Scholar Dashboard" button
- [ ] User is assigned "Scholar" role
- [ ] Scholar record is created in database

**Troubleshooting:**
```bash
# Check logs
tail -f storage/logs/laravel.log | grep -i "approval"

# Verify scholar role
php artisan tinker
>>> $user = User::where('email', 'test@example.com')->first();
>>> $user->hasRole('Scholar');
```

---

### 5. Application Rejection

**Test Steps:**
1. Log in to Filament admin panel
2. Navigate to Applications
3. Click "Reject" action on an application
4. Confirm the action
5. Check applicant's email inbox

**Expected Results:**
- [ ] Rejection email received within 1 minute
- [ ] Email subject: "LGF Scholarship Application Update"
- [ ] Email is professional and encouraging
- [ ] Email contains "View Application" button
- [ ] Application status is "rejected" in database

**Troubleshooting:**
```bash
# Check logs
tail -f storage/logs/laravel.log | grep -i "rejection"
```

---

### 6. System User Creation

**Test Steps:**
1. Log in to Filament admin panel
2. Navigate to System Users
3. Click "Create" button
4. Fill in user details (leave password blank to auto-generate)
5. Assign role (System Admin or Committee Member)
6. Save
7. Check new user's email inbox

**Expected Results:**
- [ ] Welcome email received within 1 minute
- [ ] Email subject: "Your LGF System Account Has Been Created"
- [ ] Email contains user's name
- [ ] Email contains user's email
- [ ] Email contains temporary password
- [ ] Email contains "Access Admin Panel" button
- [ ] Email lists assigned role(s)
- [ ] User can log in with provided credentials

**Troubleshooting:**
```bash
# Check logs
tail -f storage/logs/laravel.log | grep -i "system user"
```

---

### 7. Password Reset (Applicant Portal)

**Test Steps:**
1. Navigate to applicant login page
2. Click "Forgot Password?"
3. Enter email address
4. Submit
5. Check email inbox

**Expected Results:**
- [ ] Password reset email received within 1 minute
- [ ] Email contains reset link
- [ ] Clicking link opens reset password page
- [ ] Can set new password successfully
- [ ] Can log in with new password

**Troubleshooting:**
```bash
# Check logs
tail -f storage/logs/laravel.log | grep -i "password"
```

---

### 8. Password Reset (Filament Admin)

**Test Steps:**
1. Navigate to Filament login page
2. Click "Forgot Password?"
3. Enter email address
4. Submit
5. Check email inbox

**Expected Results:**
- [ ] Password reset email received within 1 minute
- [ ] Email contains reset link
- [ ] Clicking link opens reset password page
- [ ] Can set new password successfully
- [ ] Can log in to admin panel with new password

---

## 🔍 Technical Verification

### Database Checks
```bash
php artisan tinker
```

```php
// Check if emails are queued
DB::table('jobs')->count();

// Check failed jobs
DB::table('failed_jobs')->count();

// Check user has email verified
$user = User::where('email', 'test@example.com')->first();
$user->hasVerifiedEmail();

// Check user roles
$user->getRoleNames();
```

### Queue Monitoring
```bash
# Watch queue in real-time
php artisan queue:work --verbose

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Log Analysis
```bash
# Check for email errors
grep -i "failed to send" storage/logs/laravel.log

# Check for successful emails
grep -i "mail" storage/logs/laravel.log | tail -20

# Watch logs in real-time
tail -f storage/logs/laravel.log
```

### Resend Dashboard Checks
- [ ] Log in to resend.com
- [ ] Navigate to Logs section
- [ ] Verify emails are being received by Resend
- [ ] Check delivery status (should be "delivered")
- [ ] Verify no bounces or complaints
- [ ] Check API usage is within limits

---

## 🚨 Common Issues & Solutions

### Issue: No emails received

**Check:**
1. Queue worker running? `ps aux | grep queue:work`
2. Jobs in queue? `DB::table('jobs')->count()`
3. Failed jobs? `php artisan queue:failed`
4. Logs show errors? `tail -f storage/logs/laravel.log`
5. Resend API key correct? Check `.env`

**Solution:**
```bash
# Restart queue worker
php artisan queue:restart
php artisan queue:work

# Clear failed jobs and retry
php artisan queue:flush
php artisan queue:retry all
```

---

### Issue: Emails in spam

**Check:**
1. Domain verified in Resend?
2. SPF record added?
3. DKIM record added?
4. DMARC record added?
5. Using professional from address?

**Solution:**
- Verify domain in Resend dashboard
- Add all DNS records
- Wait 24-48 hours for DNS propagation

---

### Issue: Queue worker stops

**Check:**
1. Any PHP errors? Check logs
2. Memory limit reached?
3. Timeout issues?

**Solution:**
```bash
# For production, use Supervisor
sudo apt-get install supervisor

# Configure supervisor to auto-restart queue worker
# See Laravel documentation for supervisor config
```

---

### Issue: Wrong email content

**Check:**
1. Template file exists?
2. Variables passed correctly?
3. Blade syntax correct?

**Solution:**
```bash
# Clear view cache
php artisan view:clear

# Test email in tinker
php artisan tinker
>>> Mail::to('test@example.com')->send(new App\Mail\WelcomeApplicant(User::first()));
```

---

## 📊 Performance Checks

### Email Sending Speed
- [ ] Emails sent within 1 minute of trigger
- [ ] Queue processing without delays
- [ ] No memory issues with queue worker

### Queue Health
```bash
# Monitor queue size
watch -n 1 'php artisan queue:monitor'

# Check queue statistics
php artisan queue:work --verbose
```

### Resend API Limits
- [ ] Within daily limit (100 for free tier)
- [ ] Within monthly limit (3,000 for free tier)
- [ ] No rate limiting errors

---

## ✅ Final Verification

### All Email Types Working
- [ ] Welcome email (registration)
- [ ] Application received
- [ ] Status update (under review)
- [ ] Application approved
- [ ] Application rejected
- [ ] System user created
- [ ] Password reset (applicant)
- [ ] Password reset (admin)

### Production Readiness
- [ ] Domain verified in Resend
- [ ] DNS records configured
- [ ] Queue worker configured with Supervisor
- [ ] Monitoring set up
- [ ] Error alerting configured
- [ ] Backup email provider configured (optional)

### Documentation
- [ ] Team trained on email system
- [ ] Troubleshooting guide accessible
- [ ] Resend credentials documented securely
- [ ] Escalation process defined

---

## 📝 Sign-Off

**Tested By:** ___________________  
**Date:** ___________________  
**Environment:** [ ] Development [ ] Staging [ ] Production  
**Status:** [ ] Pass [ ] Fail  
**Notes:** ___________________

---

**Next Steps After Verification:**
1. Document any issues found
2. Fix issues and re-test
3. Deploy to staging/production
4. Monitor for 24 hours
5. Collect user feedback
