<x-mail::message>
# Application Update

Dear {{ $application->user->name ?? 'Applicant' }},

Thank you for your interest in the Luigi Giussani Foundation Scholarship program and for taking the time to submit your application.

After careful consideration by our selection committee, we regret to inform you that we are unable to offer you a scholarship at this time. We received an overwhelming number of qualified applications, and the selection process was highly competitive.

We encourage you to continue pursuing your educational goals and wish you the very best in your academic journey.

<x-mail::button :url="config('app.url') . '/portal'">
View Application
</x-mail::button>

Thank you again for your interest in our program.

Warm regards,<br>
{{ config('app.name') }}
</x-mail::message>
