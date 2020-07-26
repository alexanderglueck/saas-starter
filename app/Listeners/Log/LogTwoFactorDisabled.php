<?php

namespace App\Listeners\Log;

use App\Events\TwoFactorDisabled;
use App\LogEntry;
use Illuminate\Support\Facades\Request;

class LogTwoFactorDisabled
{
    /**
     * Handle the event.
     *
     * @param TwoFactorDisabled $event
     *
     * @return void
     */
    public function handle(TwoFactorDisabled $event)
    {
        if ( ! config('boilerplate.log.tf_disabled')) {
            return;
        }

        $logEntry = new LogEntry();
        $logEntry->user_id = $event->user->id;
        $logEntry->event = 'auth.tfa_disabled';
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
