<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Webklex\IMAP\Events\MessageNewEvent::class => [
            \App\Listeners\LogNewMessage::class,
        ],
    ];

    public function boot(): void
    {
        // You can add any additional bootstrapping logic here if needed.
    }
}
