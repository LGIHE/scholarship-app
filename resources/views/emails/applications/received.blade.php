<x-mail::message>
# Application Received

Dear {{ $application->user->name ?? 'Applicant' }},

We have successfully received your application for the Luigi Giussani Foundation Scholarship.

Our committee will review your detailed application. We will reach out once a decision has been made.

<x-mail::button :url="config('app.url') . '/portal'">
View Application Status
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
