<div class="nav flex-column nav-pills">
    <a class="nav-link {{ return_if(on_page('*/profile'), ' active') }}" href="{{ route('user_settings.profile.show') }}">
        {{ __('ui.settings.profile.title') }}
    </a>

    <a class="nav-link {{ return_if(on_page('*/password'), ' active') }}" href="{{ route('user_settings.password.show') }}">
        {{ __('ui.settings.password.title') }}
    </a>

    <a class="nav-link {{ return_if(on_page('*/image'), ' active') }}" href="{{ route('user_settings.image.show') }}">
        {{ __('ui.settings.profile_image.title') }}
    </a>

    <a class="nav-link {{ return_if(on_page('*/two-factor*'), ' active') }}" href="{{ route('user_settings.two-factor.show') }}">
        {{ __('ui.settings.two_factor_authentication.title') }}
    </a>

    <a class="nav-link {{ return_if(on_page('*/api-token'), ' active') }}" href="{{ route('user_settings.api_token.show') }}">
        {{ __('ui.settings.api_token.title') }}
    </a>

    <a class="nav-link {{ return_if(on_page('*/deactivate'), ' active') }}" href="{{ route('user_settings.deactivate.index') }}">
        {{ trans('ui.settings.deactivate.title') }}
    </a>

    <a class="nav-link {{ return_if(on_page('*/delete-account'), ' active') }}" href="{{ route('user_settings.delete.show') }}">
        {{ trans('ui.settings.delete.title') }}
    </a>
</div>

@subscribed
@notpiggybacksubscription
<hr>
<div class="nav flex-column nav-pills">
    @subscriptionnotcancelled
    <a class="nav-link {{ return_if(on_page('*/subscription/swap'), ' active') }}"
       href="{{ route('user_settings.subscription.swap.index') }}"
    >
        Change plan
    </a>

    <a class="nav-link {{ return_if(on_page('*/subscription/cancel'), ' active') }}"
       href="{{ route('user_settings.subscription.cancel.index') }}"
    >
        Cancel subscription
    </a>
    @endsubscriptionnotcancelled

    @subscriptioncancelled
    <a class="nav-link {{ return_if(on_page('*/subscription/resume'), ' active') }}"
       href="{{ route('user_settings.subscription.resume.index') }}"
    >
        Resume subscription
    </a>
    @endsubscriptioncancelled

    <a class="nav-link {{ return_if(on_page('*/subscription/card'), ' active') }}"
       href="{{ route('user_settings.subscription.card.index') }}"
    >
        Update card
    </a>

    @teamsubscription
    <a class="nav-link {{ return_if(on_page('*/subscription/team'), ' active') }}"
       href="{{ route('teams.index') }}"
    >
        Manage team
    </a>
    @endteamsubscription


</div>
@endnotpiggybacksubscription
@endsubscribed

@if (auth()->user()->hasStripeId())
<hr>
<div class="nav flex-column nav-pills">
    <a class="nav-link {{ return_if(on_page('*/subscription/invoices'), ' active') }}"
       href="{{ route('user_settings.subscription.invoices.index') }}"
    >
        Invoices
    </a>
</div>
@endif
