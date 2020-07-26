@component('mail::message')
# Two factor authentication disabled

We're just letting you know that two factor authentication was removed from your account.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
