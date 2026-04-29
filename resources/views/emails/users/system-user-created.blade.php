<x-mail::message>
# Welcome to LGF Admin System

Dear {{ $user->name }},

Your administrator account has been created for the Luigi Giussani Foundation admin panel.

**Email:** {{ $user->email }}

To complete your account setup, please click the button below to set your password:

<x-mail::button :url="$setupUrl">
Set Your Admin Password
</x-mail::button>

**Important Security Notes:**
- This link will take you to the secure admin panel password setup
- You will be asked to create a secure password
- After setting your password, you can access the admin panel at {{ config('app.url') }}/admin

Your account has been assigned the following role(s):
@foreach($user->roles as $role)
- {{ $role->name }}
@endforeach

If you have any questions or did not expect to receive this email, please contact the system administrator immediately.

Best regards,<br>
{{ config('app.name') }}

---

*If the button doesn't work, copy and paste this link into your browser:*  
{{ $setupUrl }}
</x-mail::message>
