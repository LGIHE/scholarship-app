<x-mail::message>
# Congratulations!

Dear {{ $application->user->name ?? 'Applicant' }},

We are thrilled to inform you that your application for the Luigi Giussani Foundation Scholarship has been **APPROVED**. 

You are now officially a Scholar and will find new resources available on your Dashboard.

<x-mail::button :url="config('app.url') . '/portal'">
Go to Scholar Dashboard
</x-mail::button>

In the coming days, our Foundation team will reach out with the next steps to properly set up your tuition payments and living stipends. Let's make an impact together!

Warm regards,<br>
{{ config('app.name') }}
</x-mail::message>
