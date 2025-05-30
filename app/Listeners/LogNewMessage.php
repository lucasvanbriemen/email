<?php

namespace App\Listeners;

use Webklex\IMAP\Events\MessageNewEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Helpers\NtfyHelper;

class LogNewMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageNewEvent $event): void
    {
        NtfyHelper::sendNofication("incoming email", "test", "https://example.com");
    }
}
