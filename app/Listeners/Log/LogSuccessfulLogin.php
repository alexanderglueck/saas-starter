<?php

namespace App\Listeners\Log;

use App\LogEntry;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param Login $event
     *
     * @return void
     */
    public function handle(Login $event)
    {
        if ( ! config('boilerplate.log.login_success')) {
            return;
        }

        $logEntry = new LogEntry();
        $logEntry->event = 'auth.succeeded';
        $logEntry->user_id = $event->user->id;
        $logEntry->ip_address = Request::ip();
        $logEntry->save();
    }
}
