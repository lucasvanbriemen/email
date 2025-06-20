<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Webklex\IMAP\Facades\Client;
use Webklex\IMAP\ClientManager;
use Illuminate\Support\Facades\Log;
use App\Helpers\NtfyHelper;
use App\Models\ImapCredentials;
use App\Models\Email;
use App\Models\Folder;
use App\Models\Attachment;

Artisan::command("get_emails", function () {

    $credentials = ImapCredentials::pluck('id');
    foreach ($credentials as $id) {
        \App\Jobs\FetchEmailsJob::dispatch($id);
    }
});

Schedule::command('get_emails')
    ->everyFifteenSeconds()
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('Emails fetched successfully at ' . now());
    })
    ->onFailure(function () {
        Log::error('Failed to fetch emails at ' . now());
    });
