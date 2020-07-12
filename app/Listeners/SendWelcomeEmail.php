<?php

namespace App\Listeners;

use App\Notifications\WelcomeEmail;
use App\User;
use Illuminate\Auth\Events\Registered;

class SendWelcomeEmail
{
    /**
     * Handle the event.
     *
     * @param Registered $event
     *
     * @return void
     */
    public function handle(Registered $event)
    {
        /** @var User $user */
        $user = $event->user;

        $user->notify(new WelcomeEmail());
    }
}
