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

                // possable folders
                // Optionally, you can also move the email to a "Trash" folder
                $trashFolder = Folder::where('name', 'LIKE', '%trash%')
                    ->where('user_id', $credential->user_id)
                    ->first();

                if (
                    Email::where('uid', $message->getUid())
                    ->where('user_id', $credential->user_id)
                    ->where('folder_id', Folder::where('path', $folder->path)
                        ->where('user_id', $credential->user_id)
                        ->value('id') ?? null)->exists()

                    ||

                    Email::where('uid', $message->getUid())
                    ->where('user_id', $credential->user_id)
                    ->where('folder_id', Folder::where('name', 'LIKE', '%trash%')->where('user_id', $credential->user_id)
                        ->where('user_id', $credential->user_id)
                        ->value('id') ?? null)->exists()
                ) {
                    continue; // Skip if email already exists
                }

                $date = $message->getAttributes()['date']->first();
                $dateUtc = $date->setTimezone(new DateTimeZone('Europe/Amsterdam'));

                $folderId = Folder::where('path', $folder->path)
                    ->where('user_id', $credential->user_id)
                    ->value('id');

                if (!$folderId) {
                    // Create folder if it doesn't exist
                    $folderId = Folder::create([
                        'user_id' => $credential->user_id,
                        'name' => $folder->name,
                        'path' => $folder->path,
                    ])->id;
                }

                // Prepare email data
                $emailData = [
                    'user_id' => $credential->user_id,
                    'subject' => $message->getSubject(),
                    'from' => $message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? null,
                    'sent_at' => $dateUtc->format('Y-m-d H:i:s'),
                    'has_read' => $message->getFlags()->has('seen'),
                    'uid' => $message->getUid(),
                    'html_body' => $message->getHTMLBody() ?: $message->getTextBody(),
                    'folder_id' => $folderId,
                    'sender_email' => $message->getFrom()[0]->mail ?? null,
                    'to' => implode(', ', collect($message->getTo()?->all() ?? [])->map(function ($to) {
                        return $to->mail ?? null;
                    })->filter()->all()) ?: null,
                ];


                $email = Email::create($emailData);

                // atchements
                if ($message->hasAttachments()) {
                    $this->info("Attachment found");

                    $attachments = $message->getAttachments();
                    foreach ($attachments as $attachment) {
                        $this->info("Processing attachment: " . $attachment->name);
                        // Save attachment to storage
                        $filePath = 'attachments/' . $credential->user_id . '/';
                        if (!file_exists(public_path($filePath))) {
                            mkdir(public_path($filePath), 0777, true);
                        }

                        $filename_to_store = uniqid() . "." . $attachment->getExtension();
                        $attachment->save(public_path($filePath), $filename_to_store);

                        // Create attachment record
                        Attachment::create([
                            'email_id' => $email->id,
                            'name' => $attachment->name,
                            'path' => $filePath . $filename_to_store,
                            'mime_type' => $attachment->mime ?? null,
                        ]);
                    }
                }


                // Send notification
                dispatch(function () use ($emailData) {
                    NtfyHelper::sendNofication(
                        $emailData['from'],
                        $emailData['subject'],
                        config('app.url') . '/folder/INBOX/mail/' . $emailData['uid']
                    );
                });
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
    ->everyFifteenSeconds()
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
