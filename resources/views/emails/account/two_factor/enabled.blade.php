@component('mail::message')
# Two factor authentication enabled

We're just letting you know that two factor authentication was added to your account.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
