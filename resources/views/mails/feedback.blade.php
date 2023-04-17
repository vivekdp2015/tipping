@component('mail::message')
# Hello!

Contact from {{ $feedback['first_name'] }} {{ $feedback['last_name'] }}

Email : {{ $feedback['email'] }},<br><br>
{{ $feedback['message'] }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
