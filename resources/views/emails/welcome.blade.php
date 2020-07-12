@component('mail::message')
# Welcome!

Thank's for signing up to {{ config('app.name') }}.

Please let us know if there is anything we can help you with.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
