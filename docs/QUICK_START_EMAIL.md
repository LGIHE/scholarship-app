# Quick Start: Email Setup

## 🚀 Get Started in 5 Minutes

### Step 1: Sign Up for Resend (2 minutes)
1. Go to [resend.com](https://resend.com)
2. Sign up for a free account
3. Navigate to **API Keys** in the dashboard
4. Click **Create API Key**
5. Copy the API key (starts with `re_`)

### Step 2: Configure Your Application (1 minute)
1. Open your `.env` file
2. Update these lines:
   ```env
   MAIL_MAILER=resend
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"
   MAIL_FROM_NAME="Luigi Giussani Foundation"
   
   RESEND_API_KEY=re_your_api_key_here
   ```
3. Replace `re_your_api_key_here` with your actual API key
4. Replace `noreply@yourdomain.com` with your email (for testing, use your personal email)

### Step 3: Start the Queue Worker (30 seconds)
Open a new terminal and run:
```bash
php artisan queue:work
```

**Keep this terminal running!** This processes email jobs.

### Step 4: Test It! (1 minute)
1. Register a new user on your application
2. Check your email inbox
3. You should receive a welcome email with verification link

## ✅ What's Working Now

All these emails are automatically sent:

| Action | Email Sent | Recipient |
|--------|-----------|-----------|
| User registers | Welcome + Email verification | New user |
| Application submitted | Submission confirmation | Applicant |
| Status → Under Review | Status update | Applicant |
| Status → Approved | Approval notification | Applicant |
| Status → Rejected | Rejection notification | Applicant |
| System user created | Login credentials | New admin user |
| Password reset requested | Reset link | Any user |

## 🔧 Development vs Production

### For Development (Testing)
```env
# Option 1: Use Resend with your personal email
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="your-email@gmail.com"
RESEND_API_KEY=re_your_key

# Option 2: Log emails to file (no actual sending)
MAIL_MAILER=log
```

### For Production
```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Luigi Giussani Foundation"
RESEND_API_KEY=re_your_production_key
```

**Important for Production:**
- You MUST verify your domain in Resend
- Add DNS records (SPF, DKIM, DMARC)
- See [RESEND_SETUP.md](RESEND_SETUP.md) for details

## 🐛 Troubleshooting

### Emails Not Sending?

**1. Is the queue worker running?**
```bash
# Check if it's running, you should see output
php artisan queue:work
```

**2. Check the logs**
```bash
tail -f storage/logs/laravel.log
```
Look for lines starting with "Failed to send"

**3. Verify your API key**
- Make sure `RESEND_API_KEY` is set in `.env`
- Check it's not revoked in Resend dashboard

**4. Check Resend dashboard**
- Go to resend.com → Logs
- See if emails are being received by Resend

### Common Issues

**"Domain not verified"**
- For testing: Use your personal email as `MAIL_FROM_ADDRESS`
- For production: Verify your domain in Resend dashboard

**"Queue not processing"**
- Make sure `php artisan queue:work` is running
- For production, set up Supervisor to keep it running

**"Emails going to spam"**
- Add SPF, DKIM, and DMARC records
- Verify domain in Resend
- Use a professional from address

## 📊 Monitoring

### Resend Dashboard
- View all sent emails
- Check delivery status
- See bounce rates
- Monitor API usage

### Application Logs
```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log | grep -i mail
```

## 🎯 Testing Checklist

Test each email type:

- [ ] Register new user → Welcome email received
- [ ] Submit application → Confirmation email received
- [ ] Change status to "Under Review" → Update email received
- [ ] Approve application → Approval email received
- [ ] Reject application → Rejection email received
- [ ] Create system user → Credentials email received
- [ ] Request password reset → Reset link received

## 💡 Pro Tips

1. **Use Queue for Better Performance**
   - All emails are queued automatically
   - Users don't wait for email to send
   - Always keep queue worker running

2. **Monitor Your Limits**
   - Free tier: 100 emails/day, 3,000/month
   - Check usage in Resend dashboard
   - Upgrade if needed

3. **Test Before Production**
   - Use log driver locally: `MAIL_MAILER=log`
   - Test with Resend using personal email
   - Verify all email types work

4. **Set Up Supervisor for Production**
   ```bash
   # Keep queue worker running automatically
   sudo apt-get install supervisor
   # Configure supervisor (see Laravel docs)
   ```

## 📚 More Information

- **Detailed Setup**: [RESEND_SETUP.md](RESEND_SETUP.md)
- **Implementation Details**: [EMAIL_IMPLEMENTATION_SUMMARY.md](EMAIL_IMPLEMENTATION_SUMMARY.md)
- **Resend Docs**: https://resend.com/docs
- **Laravel Mail Docs**: https://laravel.com/docs/mail

## 🆘 Need Help?

1. Check [RESEND_SETUP.md](RESEND_SETUP.md) troubleshooting section
2. Review application logs: `storage/logs/laravel.log`
3. Check Resend dashboard for delivery issues
4. Verify queue worker is running

---

**Remember**: Keep `php artisan queue:work` running while testing!
