<x-mail::message>
# Welcome to MaintainXtra!

Hello **{{ $technician->name }}**,

Welcome to MaintainXtra! You have been added as a technician by **{{ $manager->name }}**.

To get started, please verify your account and set up your password by clicking the button below:

<x-mail::button :url="route('password.reset', ['token' => $verification_token, 'email' => $technician->email])">
Verify Your Account
</x-mail::button>

**Your Account Details:**
- **Name:** {{ $technician->name }}
- **Email:** {{ $technician->email }}
- **Phone:** {{ $technician->phone }}
- **Added by:** {{ $manager->name }}

This verification link will expire in 24 hours for security purposes. If you need a new link, please contact your manager.

Once you've set up your password, you can log in to the system and start managing maintenance requests.

If you have any questions, please don't hesitate to contact your manager.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> 