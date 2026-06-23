@component('mail::message')
# Hello {{ $user->name }},

Your account has been set up on **{{ setting('site_title') ?: config('app.name') }}**. You can now log in using the credentials provided below.

@component('mail::panel')
**Email:** {{ $user->email }}<br>
**Password:** {{ $password }}
@endcomponent

For security reasons, we recommend that you change your password after your first login.

@component('mail::button', ['url' => route('login')])
Login to Your Account
@endcomponent

If you did not expect this email, no further action is required.

Thanks,<br>
{{ setting('site_title') ?: config('app.name') }}
@endcomponent