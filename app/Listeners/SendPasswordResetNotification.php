<?php

namespace App\Listeners;

use App\Notifications\PasswordReset as PasswordResetNotification;
use Illuminate\Auth\Events\PasswordReset;

class SendPasswordResetNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PasswordReset $event)
    {
        $event->user->notify(new PasswordResetNotification);
    }
}
