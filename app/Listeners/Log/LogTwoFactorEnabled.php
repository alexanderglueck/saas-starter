<?php

namespace App\Listeners\Log;

use App\Events\TwoFactorEnabled;
use App\LogEntry;
use Illuminate\Support\Facades\Request;

class LogTwoFactorEnabled
{
    /**
     * Handle the event.
     *
     * @param TwoFactorEnabled $event
     *
     * @return void
     */
    public function handle(TwoFactorEnabled $event)
    {
        if ( ! config('boilerplate.log.tf_enabled')) {
            return;
        }

        $logEntry = new LogEntry();
        $logEntry->user_id = $event->user->id;
        $logEntry->event = 'auth.tfa_enabled';
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
