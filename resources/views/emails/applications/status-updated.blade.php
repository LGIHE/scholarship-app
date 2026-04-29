<x-mail::message>
# Application Status Update

Dear {{ $application->user->name ?? 'Applicant' }},

Your Luigi Giussani Foundation Scholarship application status has been updated.

**Previous Status:** {{ ucfirst(str_replace('_', ' ', $oldStatus)) }}  
**New Status:** {{ ucfirst(str_replace('_', ' ', $newStatus)) }}

@if($newStatus === 'under_review')
Your application is currently being reviewed by our committee. We will notify you once a decision has been made.
@endif

<x-mail::button :url="config('app.url') . '/portal'">
View Application Status
</x-mail::button>

If you have any questions, please don't hesitate to contact us.

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
