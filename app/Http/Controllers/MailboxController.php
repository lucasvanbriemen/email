<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use App\Models\Email;
use App\Models\Folder;
use App\Models\Attachment;
use App\Models\User;
use App\Models\ImapCredentials;

class MailboxController extends Controller
{
    protected $client;
    protected $DEFAULT_FOLDER = 'inbox';

    public function index($credential_id = null, $folder = null)
    {
        $selectedFolder = $folder ?: $this->DEFAULT_FOLDER;

        if (!$credential_id) {
            $credential_id = ImapCredentials::where('user_id', auth()->id())
                ->value('id');

            // If that doesnt exist redircht to /account
            if (!$credential_id) {
                return redirect('/account');
            }
        }

        $folder = Folder::where('path', $selectedFolder)
            ->where('imap_credential_id', $credential_id)
            ->first();

        $emails = Email::getEmails($folder, $credential_id);

        $emailThreads = [];
        $email_sorted_uuids = [];

        foreach ($emails as $email) {
            // Skip already sorted emails
            if (in_array($email->uuid, $email_sorted_uuids)) {
                continue;
            }

            $currentThread = [];

            foreach ($emails as $threadEmail) {
                if (
                    $email->sender === $threadEmail->sender &&
                    $email->subject === $threadEmail->subject
                ) {
                    $currentThread[] = $threadEmail;
                    $email_sorted_uuids[] = $threadEmail->uuid;
                }
            }

            $emailThreads[] = $currentThread;
        }

        return view('overview', [
            'emailThreads' => $emailThreads,
            'folder' => $folder,
            'selectedFolder' => $selectedFolder,
            'selectedCredential' => ImapCredentials::find($credential_id),
        ]);
    }

    public function show($credential_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('credential_id', $credential_id)
            ->first();

        $selectedFolder = $email->folder->path;

        $attachments = Attachment::where('email_id', $email->id)->get();

        $email->has_read = true;
        $email->save();

        return view('email', [
            'email' => $email,
            'selectedFolder' => $selectedFolder,
            'attachments' => $attachments,
            'selectedCredential' => ImapCredentials::find($credential_id),
        ]);
    }

    public function archive($credential_id = null, $folder = null, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('credential_id', $credential_id)
            ->first();

        if ($email) {
            $email->is_archived = true;
            $email->save();
        }

        return [
            'status' => 'success',
            'message' => 'Email archived successfully.'
        ];
    }

    public function read($credential_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('credential_id', $credential_id)
            ->first();

        if ($email) {
            $email->has_read = true;
            $email->save();
        }

        return [
            'status' => 'success',
            'message' => 'Email marked as read successfully.'
        ];
    }

    public function delete($credential_id, $folder, $uuid)
    {
        Email::deleteEmail($uuid, $credential_id);

        return [
            'status' => 'success',
            'message' => 'Email deleted successfully.'
        ];
    }

    public function star($credential_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('credential_id', $credential_id)
            ->first();

        if ($email) {
            $email->is_starred = true;
            $email->save();
        }

        return [
            'status' => 'success',
            'message' => 'Email starred successfully.'
        ];
    }

    public function archiveThread($credential_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('credential_id', $credential_id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('credential_id', $credential_id)
            ->get();

        foreach ($emails as $threadEmail) {
            $threadEmail->is_archived = true;
            $threadEmail->save();
        }

        return [
            'status' => 'success',
            'message' => 'Thread archived successfully.'
        ];
    }

    public function deleteThread($credential_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('credential_id', $credential_id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('credential_id', $credential_id)
            ->get();

        foreach ($emails as $threadEmail) {
            Email::deleteEmail($threadEmail->uuid, $threadEmail->credential_id);
        }

        return [
            'status' => 'success',
            'message' => 'Thread deleted successfully.'
        ];
    }

    public function starThread($credential_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('credential_id', $credential_id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('credential_id', $credential_id)
            ->get();

        foreach ($emails as $threadEmail) {
            $threadEmail->is_starred = true;
            $threadEmail->save();
        }

        return [
            'status' => 'success',
            'message' => 'Thread starred successfully.'
        ];
    }
}
