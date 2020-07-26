<?php

namespace App\Listeners\Auth;

use App\Events\ProfileEmailUpdated;
use App\Mail\Account\EmailUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailChangedEmail implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param ProfileEmailUpdated $event
     *
     * @return void
     */
    public function handle(ProfileEmailUpdated $event)
    {
        if ( ! config('boilerplate.send.email_changed')) {
            return;
        }

        Mail::to($event->oldEmail)->send(new EmailUpdated());
    }
}
