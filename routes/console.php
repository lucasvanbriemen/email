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
            $folder = $client->getFolder("INBOX");
        } catch (\Exception $e) {
            $this->info('Failed to fetch emails: ' . $e->getMessage());
            continue;
        }

        $messages = $folder->messages()->all()->get();

        if ($messages->isEmpty()) {
            continue; // Skip if no messages found
        }

        foreach ($messages as $message) {
            // possable folders
            // Optionally, you can also move the email to a "Trash" folder

            $date = $message->getAttributes()['date']->first();
            $dateUtc = $date->setTimezone(new DateTimeZone('Europe/Amsterdam'));
            if (
                Email::where('uid', $message->getUid())
                ->where('credential_id', $credential->id)
                ->where('sender_email', $message->getFrom()[0]->mail ?? null)
                ->where('sent_at', $dateUtc->format('Y-m-d H:i:s'))
                ->exists()
            ) {
                continue; // Skip if email already exists
            }

            $folderId = Folder::where('path', 'inbox')
                ->where('imap_credential_id', $credential->id)
                ->value('id');

            // Prepare email data
            $emailData = [
                'credential_id' => $credential->id,
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
                $attachments = $message->getAttachments();
                foreach ($attachments as $attachment) {
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
            dispatch(function () use ($emailData, $credential) {
                NtfyHelper::sendNofication(
                    $emailData['from'],
                    $emailData['subject'],
                    config('app.url') . '/' . $credential->id  . '/inbox/inbox/mail/' . $emailData['uid']
                );
            });
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
