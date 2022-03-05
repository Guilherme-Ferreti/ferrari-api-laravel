<?php

namespace App\Listeners;

use App\Events\PasswordUpdated;
use App\Notifications\PasswordUpdated as PasswordUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPasswordUpdatedNotification
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
    public function handle(PasswordUpdated $event)
    {
        $event->user->notify(new PasswordUpdatedNotification);
    }
}
