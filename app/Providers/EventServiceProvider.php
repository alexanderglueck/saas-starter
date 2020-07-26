<?php

namespace App\Providers;

use App\Contracts\TwoFactorListener;
use App\Events\ProfileEmailUpdated;
use App\Events\ProfileUpdated;
use App\Events\SubscriptionCreated;
use App\Events\TwoFactorDisabled;
use App\Events\TwoFactorEnabled;
use App\Listeners\Auth\SendEmailChangedEmail;
use App\Listeners\Auth\SendTwoFactorDisabledEmail;
use App\Listeners\Auth\SendTwoFactorEnabledEmail;
use App\Listeners\EnforceTwoFactorAuth;
use App\Listeners\Log\LogFailedLogin;
use App\Listeners\Log\LogSuccessfulLogin;
use App\Listeners\Log\LogTwoFactorDisabled;
use App\Listeners\Log\LogTwoFactorEnabled;
use App\Listeners\SendThankYouEmail;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Validated;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            SendWelcomeEmail::class
        ],
        Attempting::class => [
            TwoFactorListener::class . '@saveCredentials'
        ],
        Validated::class => [
            TwoFactorListener::class . '@checkTwoFactor'
        ],
        Login::class => [
            LogSuccessfulLogin::class,
        ],
        Failed::class => [
            LogFailedLogin::class,
        ],
        \App\Events\Auth\UserRequestedActivationEmail::class => [
            \App\Listeners\Auth\SendActivationEmail::class,
        ],
        \Illuminate\Auth\Events\PasswordReset::class => [
            \App\Listeners\Auth\SendPasswordChangedEmail::class,
        ],

        \App\Events\Auth\UserChangedPassword::class => [
            \App\Listeners\Auth\SendPasswordChangedEmail::class,
        ],
        TwoFactorEnabled::class => [
            LogTwoFactorEnabled::class,
            SendTwoFactorEnabledEmail::class
        ],
        TwoFactorDisabled::class => [
            LogTwoFactorDisabled::class,
            SendTwoFactorDisabledEmail::class
        ],
        ProfileEmailUpdated::class => [
            SendEmailChangedEmail::class
        ],
        SubscriptionCreated::class => [
            SendThankYouEmail::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->registerTwoFactorAuthListener();
    }

    public function registerTwoFactorAuthListener(): void
    {
        $this->app->singleton(TwoFactorListener::class, function ($app) {
            return new EnforceTwoFactorAuth($app['request']);
        });
    }
}
