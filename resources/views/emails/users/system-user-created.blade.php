<x-mail::message>
# Welcome to LGF System

Dear {{ $user->name }},

Your system account has been created for the Luigi Giussani Foundation administration panel.

**Email:** {{ $user->email }}  
**Temporary Password:** {{ $temporaryPassword }}

**Important:** Please change your password immediately after your first login for security purposes.

<x-mail::button :url="config('app.url') . '/admin'">
Access Admin Panel
</x-mail::button>

Your account has been assigned the following role(s):
@foreach($user->roles as $role)
- {{ $role->name }}
@endforeach

If you have any questions or did not expect to receive this email, please contact the system administrator immediately.

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
