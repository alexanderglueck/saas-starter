<?php

namespace App\Listeners\Auth;

use App\Mail\Account\TwoFactorDisabled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendTwoFactorDisabledEmail implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        if ( ! config('boilerplate.send.tf_disabled')) {
            return;
        }

        Mail::to($event->user)->send(new TwoFactorDisabled());
    }
}
