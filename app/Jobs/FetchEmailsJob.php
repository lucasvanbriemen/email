<?php

namespace App\Jobs;

use App\Models\ImapCredentials;
use App\Models\Email;
use App\Models\Folder;
use App\Models\Attachment;
use App\Helpers\NtfyHelper;
use Webklex\IMAP\Facades\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DateTimeZone;

class FetchEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $credentialId;

    public function __construct($credentialId)
    {
        $this->credentialId = $credentialId;
    }

    public function handle()
    {
        $credential = ImapCredentials::find($this->credentialId);

        if (!$credential) {
            return;
        }

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
            Log::error("Failed IMAP connect [{$credential->id}]: " . $e->getMessage());
            return;
        }

        try {
            $folder = $client->getFolder("INBOX");
        } catch (\Exception $e) {
            Log::error("Failed to get folder [{$credential->id}]: " . $e->getMessage());
            return;
        }

        $messages = $folder->messages()->all()->get();

        if ($messages->isEmpty()) {
            return;
        }

        foreach ($messages as $message) {
            $date = $message->getAttributes()['date']->first();
            $dateUtc = $date->setTimezone(new DateTimeZone('Europe/Amsterdam'));

            if (
                Email::where('uid', $message->getUid())
                ->where('credential_id', $credential->id)
                ->where('sender_email', $message->getFrom()[0]->mail ?? null)
                ->where('sent_at', $dateUtc->format('Y-m-d H:i:s'))
                ->exists()
            ) {
                $message->delete();
                continue;
            }

            $folderId = Folder::where('path', 'inbox')
                ->where('imap_credential_id', $credential->id)
                ->value('id');

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

            if ($message->hasAttachments()) {
                $attachments = $message->getAttachments();
                foreach ($attachments as $attachment) {
                    $filePath = 'attachments/' . $credential->user_id . '/';
                    if (!file_exists(public_path($filePath))) {
                        mkdir(public_path($filePath), 0777, true);
                    }

                    $filename_to_store = uniqid() . "." . $attachment->getExtension();
                    $attachment->save(public_path($filePath), $filename_to_store);

                    Attachment::create([
                        'email_id' => $email->id,
                        'name' => $attachment->name,
                        'path' => $filePath . $filename_to_store,
                        'mime_type' => $attachment->mime ?? null,
                    ]);
                }
            }

            $message->delete();

            if (!config('app.ntfy.enabled')) {
                continue;
            }

            NtfyHelper::sendNofication(
                $emailData['from'],
                $emailData['subject'],
                config('app.url') . '/' . $credential->id  . '/folder/inbox/mail/' . $email['uuid']
            );
        }
    }
}
