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

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command("get_emails", function () {
    $imapCredentials = ImapCredentials::all();

    // Loop through each IMAP credential
    foreach ($imapCredentials as $credential) {
        try {
            $client = Client::make([
                'host'          => $credential->host,
                'port'          => $credential->port,
                'protocol'      => $credential->protocol ?? 'imap',
                'encryption'    => $credential->encryption ?? 'ssl',
                'validate_cert' => $credential->validate_cert ?? true,
                'username'      => $credential->username,
                'password'      => $credential->password,
            ]);

            $client->connect();
        } catch (\Exception $e) {
            $this->info('Failed to connect to IMAP server: ' . $e->getMessage());
            continue;
        }

        // Fetch emails from INBOX
        try {
            $folder = $client->getFolder('INBOX');
            $messages = $folder->messages()->all()->get();
        } catch (\Exception $e) {
            $this->info('Failed to fetch emails: ' . $e->getMessage());
            continue;
        }

        // Process each email
        foreach ($messages as $message) {
            // Check if the email already exists in the database

            if (Email::where('uid', $message->getUid())->exists()) {
                continue; // Skip if email already exists
            }

            // Prepare email data
            $emailData = [
                'user_id' => $credential->user_id,
                'subject' => $message->getSubject(),
                'from' => $message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? null,
                'sent_at' => date($message->getDate()),
                'has_read' => $message->getFlags()->has('seen'),
                'uid' => $message->getUid(),
                'html_body' => $message->getHTMLBody() ?: $message->getTextBody(),
            ];

            Email::create($emailData);

            // Send notification
            // NtfyHelper::sendNofication(
            //     $emailData['from'],
            //     $emailData['subject'],
            //     config('app.url') . '/folder/INBOX/mail/' . $emailData['uid']
            // );
        }
    }
});

Schedule::command('get_emails')
    ->everyMinute()
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('Emails fetched successfully at ' . now());
    })
    ->onFailure(function () {
        Log::error('Failed to fetch emails at ' . now());
    });
