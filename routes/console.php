<?php

use Illuminate\Support\Facades\Artisan;
use App\Helpers\NotifyHelper;

Artisan::command('ntfy', function () {
    NotifyHelper::sendPushCutNotification('Test Notification', 'This is a test notification from the console command.', 'https://example.com');
});
