<x-mail::message>
# Welcome to Luigi Giussani Foundation!

Dear {{ $user->name }},

Thank you for registering with the Luigi Giussani Foundation Scholarship Program. We're excited to have you join us!

To complete your registration, please verify your email address by clicking the button below:

<x-mail::button :url="$url">
Verify Email Address
</x-mail::button>

Once your email is verified, you can:
- Complete your scholarship application
- Track your application status
- Access important updates and resources

If you did not create an account, no further action is required.

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
