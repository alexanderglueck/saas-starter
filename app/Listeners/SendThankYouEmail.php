<?php

namespace App\Listeners;

use App\Events\SubscriptionCreated;
use App\Mail\Subscription\ThankYou;
use Illuminate\Support\Facades\Mail;

class SendThankYouEmail
{
    /**
     * Handle the event.
     *
     * @param SubscriptionCreated $event
     * @return void
     */
    public function handle(SubscriptionCreated $event)
    {
        if ( ! config('boilerplate.send.subscription_created')) {
            return;
        }

        Mail::to($event->user)->send(new ThankYou());
    }
}
