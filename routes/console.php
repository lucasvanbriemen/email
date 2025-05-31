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

        // Fetch folders
        try {
            $folders = $client->getFolders(false);
        } catch (\Exception $e) {
            $this->info('Failed to fetch emails: ' . $e->getMessage());
            continue;
        }

        // Process each email

        foreach ($folders as $folder) {
            $messages = $folder->messages()->all()->get();

            if ($messages->isEmpty()) {
                continue; // Skip if no messages found
            }

            foreach ($messages as $message) {
                if (
                    Email::where('uid', $message->getUid())
                    ->where('user_id', $credential->user_id)
                    ->where('folder_id', Folder::where('path', $folder->path)
                        ->where('user_id', $credential->user_id)
                        ->value('id') ?? null)->exists())
                {
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
                    'folder_id' => Folder::where('path', $folder->path)->where('user_id', $credential->user_id)->value('id') ?: null,
                    'sender_email' => $message->getFrom()[0]->mail ?? null,
                    'to' => implode(', ', collect($message->getTo()?->all() ?? [])->map(function ($to) {
                        return $to->mail ?? null;
                    })->filter()->all()) ?: null,
                ];

                Email::create($emailData);

                // // Send notification
                NtfyHelper::sendNofication(
                    $emailData['from'],
                    $emailData['subject'],
                    config('app.url') . '/folder/INBOX/mail/' . $emailData['uid']
                );
            }
        }
    }
});

Artisan::command('get_folders', function () {
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

        // Fetch folders
        try {
            $folders = $client->getFolders(false);
        } catch (\Exception $e) {
            $this->info('Failed to fetch folders: ' . $e->getMessage());
            continue;
        }

        // Process each folder
        foreach ($folders as $folder) {
            if (Folder::where('path', $folder->path)->exists() && Folder::where('user_id', $credential->user_id)->where('name', $folder->name)->exists()) {
                continue; // Skip if folder already exists
            }

            Folder::create([
                'user_id' => $credential->user_id,
                'name' => $folder->name,
                'path' => $folder->path,
            ]);
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

Schedule::command('get_folders')
    ->everyOddHour()
    ->withoutOverlapping()
    ->onSuccess(function () {
                Log::info('Folders fetched successfully at ' . now());
    })
    ->onFailure(function () {
                Log::error('Failed to fetch folders at ' . now());
    });
