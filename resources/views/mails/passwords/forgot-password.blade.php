{{-- @component('mail::message')
# Hello!

You are receiving this email because we received a password reset request for your account.

@component('mail::button', ['url' => env("APP_URL", 'http://tipping-jar.preview.cx').'/response-forgot-password?token='.$token.'&email='.$email])
Reset Password
@endcomponent

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent --}}

<a href="http://tipping-jar.preview.cx/response-forgot-password?token={{ $token }}&email={{ $email }}">Reset Password</a>
