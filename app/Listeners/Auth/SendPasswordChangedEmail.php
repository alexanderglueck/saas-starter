<?php

namespace App\Listeners\Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\Account\PasswordUpdated;
use App\Events\Auth\UserChangedPassword;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPasswordChangedEmail implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param UserChangedPassword $event
     *
     * @return void
     */
    public function handle(UserChangedPassword $event)
    {
        if ( ! config('boilerplate.send.pw_changed')) {
            return;
        }

        Mail::to($event->user)->send(new PasswordUpdated());
    }
}
