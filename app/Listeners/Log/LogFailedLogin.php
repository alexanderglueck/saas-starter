<?php

namespace App\Listeners\Log;

use App\LogEntry;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Request;

class LogFailedLogin
{
    /**
     * Handle the event.
     *
     * @param  Failed $event
     *
     * @return void
     */
    public function handle(Failed $event)
    {
        if ( ! isset($event->user->id)) {
            return;
        }

        $logEntry = new LogEntry();
        $logEntry->user_id = $event->user->id;
        $logEntry->event = 'auth.failed';
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
