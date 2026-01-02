<?php

namespace App\Services;

use App\Helpers\NtfyHelper;
use App\Models\ImapCredentials;
use App\Models\Email;
use App\Models\Folder;
use App\Models\Attachment;
use App\Models\IncomingEmailSender;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\Client;
use Webklex\IMAP\Facades\Client as ClientFacade;

class EmailFetchingService
{
    /**
     * Connect to IMAP server with given credentials
     */
    public function connectToImap(ImapCredentials $credential): ?Client
    {
        $client = ClientFacade::make([
            'host'          => $credential->host,
            'port'          => $credential->port,
            'protocol'      => $credential->protocol ?? 'imap',
            'encryption'    => $credential->encryption ?? 'ssl',
            'validate_cert' => $credential->validate_cert ?? true,
            'username'      => $credential->username,
            'password'      => $credential->password,
        ]);

        $client->connect();
        return $client;
    }

    /**
     * Fetch all messages from INBOX folder
     */
    public function fetchInboxMessages(Client $client)
    {
        $folder = $client->getFolder("INBOX");
        return $folder->messages()->all()->get();
    }

    /**
     * Process a single message
     */
    public function processMessage($message, ImapCredentials $credential, int $folderId): ?Email
    {
        // Get message date
        $date = $message->getAttributes()['date']->first();
        $dateUtc = $date->setTimezone(new DateTimeZone('Europe/Amsterdam'));

        // Check if email already exists
        if ($this->emailExists($message, $credential, $dateUtc)) {
            // Delete from server to avoid long run time
            try {
                $message->delete();
            } catch (Exception $e) {
                Log::warning('Failed to delete duplicate email from server: ' . $e->getMessage());
            }
            return null;
        }

        // Get or create sender
        $incomingEmailSender = $this->createOrUpdateSender($message);

        // Prepare email data
        $emailData = $this->prepareEmailData($message, $credential, $dateUtc, $folderId, $incomingEmailSender);

        // Check if email should be filtered
        if ($this->shouldFilterMessage($emailData)) {
            // Delete from server to avoid long run time
            try {
                $message->delete();
            } catch (Exception $e) {
                Log::warning('Failed to delete filtered email from server: ' . $e->getMessage());
            }
            return null;
        }

        // Save email
        $email = Email::create($emailData);

        // Handle attachments
        if ($message->hasAttachments()) {
            $this->handleAttachments($message, $email, $credential);
        }

        // Send notification
        $this->dispatchNotification($email);

        return $email;
    }

    /**
     * Check if email already exists in database
     */
    private function emailExists($message, ImapCredentials $credential, $dateUtc): bool
    {
        return Email::where('uid', $message->getUid())
            ->where('profile_id', $credential->profile_id)
            ->where('sent_at', $dateUtc->format('Y-m-d H:i:s'))
            ->whereHas('sender', function ($query) use ($message) {
                $query->where('email', $message->getFrom()[0]->mail ?? null);
            })
            ->exists();
    }

    /**
     * Create or update sender information
     */
    public function createOrUpdateSender($message): ?IncomingEmailSender
    {
        $sender = $message->getFrom()[0]->mail ?? null;

        if (!$sender) {
            return null;
        }

        try {
            return IncomingEmailSender::firstOrCreate(
                ['email' => $sender],
                [
                    'name' => $message->getFrom()[0]->personal ?? $sender,
                    'top_level_domain' => IncomingEmailSender::email_to_domain($sender),
                ]
            );
        } catch (Exception $e) {
            Log::error('Failed to create IncomingEmailSender: ' . $e->getMessage());

            // Create a basic sender without the logo functionality
            $incomingEmailSender = new IncomingEmailSender([
                'email' => $sender,
                'name' => $message->getFrom()[0]->personal ?? $sender,
                'top_level_domain' => IncomingEmailSender::email_to_domain($sender),
            ]);
            $incomingEmailSender->saveQuietly();

            return $incomingEmailSender;
        }
    }

    /**
     * Prepare email data for storage
     */
    private function prepareEmailData($message, ImapCredentials $credential, $dateUtc, int $folderId, $incomingEmailSender): array
    {
        $sender = $message->getFrom()[0]->mail ?? null;

        return [
            'profile_id' => $credential->profile_id,
            'subject' => $message->getSubject(),
            'sent_at' => $dateUtc->format('Y-m-d H:i:s'),
            'has_read' => $message->getFlags()->has('seen'),
            'uid' => $message->getUid(),
            'html_body' => $message->getHTMLBody() ?: $message->getTextBody(),
            'folder_id' => $folderId,
            'sender_id' => $incomingEmailSender ? $incomingEmailSender->id : null,
            'sender_name' => $message->getFrom()[0]->personal ?? $sender ?? null,
            'to' => implode(', ', collect($message->getTo()?->all() ?? [])->map(function ($to) {
                return $to->mail ?? null;
            })->filter()->all()) ?: null,
        ];
    }

    /**
     * Check if email should be filtered out
     */
    public function shouldFilterMessage(array $emailData): bool
    {
        // Filter out CI-related emails
        if (str_contains(strtolower($emailData['subject'] ?? ''), 'run cancelled: ci')) {
            return true;
        }

        // Filter out GitHub push notifications
        // Pattern: @mention pushed number commit(s)
        if (preg_match('/^@\w+.*pushed\s+\d+\s+commit(s)?/is', $emailData['html_body'] ?? '')) {
            return true;
        }

        // Filter out GitHub merge notifications
        // Pattern: Merged #number into
        if (preg_match('/Merged\s+#\d+\s+into/is', $emailData['html_body'] ?? '')) {
            return true;
        }

        return false;
    }

    /**
     * Save email attachments
     */
    public function handleAttachments($message, Email $email, ImapCredentials $credential): void
    {
        try {
            $attachments = $message->getAttachments();

            foreach ($attachments as $attachment) {
                $filePath = 'attachments/' . $credential->profile_id . '/';

                // Create directory if it doesn't exist
                if (!file_exists(public_path($filePath))) {
                    @mkdir(public_path($filePath), 0777, true);
                }

                // Generate unique filename
                $filename_to_store = uniqid() . "." . $attachment->getExtension();

                // Save attachment file
                $attachment->save(public_path($filePath), $filename_to_store);

                // Create attachment record
                Attachment::create([
                    'email_id' => $email->id,
                    'name' => $attachment->name,
                    'path' => $filePath . $filename_to_store,
                    'mime_type' => $attachment->mime ?? null,
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to handle attachments for email ' . $email->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Dispatch notification for new email
     */
    public function dispatchNotification(Email $email): void
    {
        if (!config('app.ntfy.enabled')) {
            return;
        }

        try {
            $url = config('app.url') . '/home/' . $email->uuid;
            $senderName = $email->getSenderDisplayName();
            $emailSubject = $email->subject;

            dispatch(function () use ($senderName, $emailSubject, $url) {
                NtfyHelper::sendNofication(
                    $senderName,
                    $emailSubject,
                    $url
                );
            });
        } catch (Exception $e) {
            Log::error('Failed to dispatch notification for email ' . $email->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Update system last_email_fetched_at timestamp
     */
    public function updateSystemLastFetchedAt(): void
    {
        DB::table('system_info')->updateOrInsert(
            ['id' => 1],
            ['last_email_fetched_at' => now()]
        );
    }

    /**
     * Get the INBOX folder ID for a profile
     */
    public function getInboxFolderId(int $profileId): ?int
    {
        return Folder::where('path', 'inbox')
            ->where('profile_id', $profileId)
            ->value('id');
    }
}
