<?php

namespace App\Listeners\Auth;

use App\Mail\Account\TwoFactorEnabled;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTwoFactorEnabledEmail implements ShouldQueue
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
        if ( ! config('boilerplate.send.tf_enabled')) {
            return;
        }

        Mail::to($event->user)->send(new TwoFactorEnabled());
    }
}
