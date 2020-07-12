<?php

namespace App\Providers;

use App\Contracts\TwoFactorListener;
use App\Listeners\EnforceTwoFactorAuth;
use App\Listeners\Log\LogFailedLogin;
use App\Listeners\Log\LogSuccessfulLogin;
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
