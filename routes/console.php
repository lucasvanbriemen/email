<?php

use Illuminate\Support\Facades\Artisan;
use App\Helpers\NtfyHelper;


Artisan::command('ntfy', function () {
    NtfyHelper::sendNofication('Test Notification', 'This is a test notification from the console command.', 'https://example.com');
});
