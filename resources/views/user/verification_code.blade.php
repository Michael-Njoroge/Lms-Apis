@component('mail::message')
Hello **{{ $user->firstname }} {{ $user->lastname }}**,

Your one time code is: {{ $code }}

Thank you!<br>
Best regards,<br>
ICT Team
@endcomponent
