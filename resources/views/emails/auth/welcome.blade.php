@component('mail::message')
# Welcome to MaintainXtra, {{ $user->name }}!

We're excited to have you on board. Your account has been created successfully.

@component('mail::button', ['url' => url('/')])
Go to Dashboard
@endcomponent

If you have any questions or need help, feel free to reply to this email.

Thanks,<br>
The MaintainXtra Team
@endcomponent 