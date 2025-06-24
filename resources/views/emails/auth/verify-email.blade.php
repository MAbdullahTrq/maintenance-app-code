@component('mail::message')
# Verify Your Email Address

Hello {{ $user->name }},

Thank you for registering with MaintainXtra! To complete your registration and activate your account, please verify your email address by clicking the button below.

@component('mail::button', ['url' => route('verification.verify', ['token' => $verificationToken, 'email' => $user->email])])
Verify Email Address
@endcomponent

This verification link will expire in 24 hours. If you did not create an account, no further action is required.

**Important:** Your account is currently inactive. You will not be able to log in until you verify your email address.

If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:
{{ route('verification.verify', ['token' => $verificationToken, 'email' => $user->email]) }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent 