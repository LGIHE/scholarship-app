# Resend Email Integration Setup

This application uses [Resend.com](https://resend.com) for sending transactional emails.

## Installation

The Resend PHP package has already been installed:

```bash
composer require resend/resend-php
```

## Configuration

### 1. Get Your Resend API Key

1. Sign up for a free account at [resend.com](https://resend.com)
2. Navigate to API Keys in your dashboard
3. Create a new API key
4. Copy the API key

### 2. Configure Environment Variables

Update your `.env` file with the following:

```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Luigi Giussani Foundation"

RESEND_API_KEY=re_your_api_key_here
```

**Important Notes:**
- Replace `noreply@yourdomain.com` with your verified domain email
- In Resend's free tier, you can only send from verified domains
- For testing, you can use Resend's test mode which delivers to your account email

### 3. Verify Your Domain (Production)

For production use, you need to verify your domain in Resend:

1. Go to Domains in your Resend dashboard
2. Add your domain
3. Add the provided DNS records (SPF, DKIM, DMARC)
4. Wait for verification (usually takes a few minutes)

### 4. Queue Configuration

All emails are queued for better performance. Make sure your queue is running:

```bash
php artisan queue:work
```

For production, use a process manager like Supervisor to keep the queue worker running.

## Email Types Implemented

### 1. Application Submission Confirmation
- **Trigger:** When an applicant submits their application
- **Recipient:** Applicant's email
- **Class:** `App\Mail\ApplicationReceived`
- **Template:** `resources/views/emails/applications/received.blade.php`

### 2. Application Status Updates
- **Trigger:** When application status changes (Under Review, Approved, Rejected)
- **Recipient:** Applicant's email
- **Classes:** 
  - `App\Mail\ApplicationStatusUpdated` (for Under Review)
  - `App\Mail\ApplicationApproved` (for Approved)
  - `App\Mail\ApplicationRejected` (for Rejected)
- **Templates:** 
  - `resources/views/emails/applications/status-updated.blade.php`
  - `resources/views/emails/applications/approved.blade.php`
  - `resources/views/emails/applications/rejected.blade.php`

### 3. Email Verification (Registration)
- **Trigger:** When an applicant registers
- **Recipient:** Applicant's email
- **Class:** `App\Mail\WelcomeApplicant`
- **Template:** `resources/views/emails/auth/welcome-applicant.blade.php`
- **Listener:** `App\Listeners\SendWelcomeEmail`

### 4. Password Reset
- **Trigger:** When user requests password reset (both applicants and Filament users)
- **Recipient:** User's email
- **Built-in:** Laravel's default password reset notification
- **Works for:** Both applicant portal and Filament admin panel

### 5. System User Creation
- **Trigger:** When a new system user is created in Filament
- **Recipient:** New user's email
- **Class:** `App\Mail\SystemUserCreated`
- **Template:** `resources/views/emails/users/system-user-created.blade.php`
- **Contains:** Login credentials with temporary password

## Testing Emails

### Local Testing (Log Driver)

For local development without Resend, use the log driver:

```env
MAIL_MAILER=log
```

Emails will be written to `storage/logs/laravel.log`

### Testing with Resend

1. Set up Resend as described above
2. Use your own email address for testing
3. Check Resend dashboard for delivery status and logs

### Artisan Tinker Testing

You can test emails using Tinker:

```bash
php artisan tinker
```

```php
// Test welcome email
$user = App\Models\User::first();
Mail::to($user)->send(new App\Mail\WelcomeApplicant($user));

// Test application received
$application = App\Models\Application::first();
Mail::to($application->user)->send(new App\Mail\ApplicationReceived($application));
```

## Monitoring

### Resend Dashboard
- View all sent emails
- Check delivery status
- View bounce and complaint rates
- Monitor API usage

### Application Logs
All email sending errors are logged to `storage/logs/laravel.log` with the prefix "Failed to send [email type] email"

## Troubleshooting

### Emails Not Sending

1. **Check Queue is Running**
   ```bash
   php artisan queue:work
   ```

2. **Check Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify API Key**
   - Ensure `RESEND_API_KEY` is set correctly
   - Check key hasn't been revoked in Resend dashboard

4. **Check From Address**
   - Must be from a verified domain
   - Or use Resend test mode for development

### Domain Verification Issues

- Ensure all DNS records are added correctly
- Wait 24-48 hours for DNS propagation
- Use Resend's DNS checker tool

### Rate Limits

Free tier limits:
- 100 emails per day
- 3,000 emails per month

Upgrade your plan if you need more capacity.

## Production Checklist

- [ ] Verify domain in Resend
- [ ] Add all DNS records (SPF, DKIM, DMARC)
- [ ] Set correct `MAIL_FROM_ADDRESS` with verified domain
- [ ] Set `RESEND_API_KEY` in production environment
- [ ] Configure queue worker with Supervisor
- [ ] Set up monitoring for failed jobs
- [ ] Test all email types in production
- [ ] Monitor Resend dashboard for delivery issues

## Support

- Resend Documentation: https://resend.com/docs
- Resend Status: https://status.resend.com
- Laravel Mail Documentation: https://laravel.com/docs/mail
