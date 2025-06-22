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

    public function index($linked_profile_id = null, $folder = null)
    {
        $selectedFolder = $folder ?: $this->DEFAULT_FOLDER;

        if (!$linked_profile_id) {
            $linked_profile_id = ImapCredentials::where('user_id', auth()->id())
                ->value('id');

            // If that doesnt exist redircht to /account
            if (!$linked_profile_id) {
                return redirect('/account');
            }
        }

        // If the selected credential does not exist to the user, show a 404 error
        if (
            !ImapCredentials::where('id', $linked_profile_id)
            ->where('user_id', auth()->id())
            ->exists()
        ) {
            abort(404);
        }

        $folder = Folder::where('path', $selectedFolder)
            ->where('imap_linked_profile_id', $linked_profile_id)
            ->first();

        $emails = Email::getEmails($folder, $linked_profile_id);

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
            'selectedCredential' => ImapCredentials::find($linked_profile_id),
        ]);
    }

    public function show($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
            ->first();

        $selectedFolder = $email->folder->path;

        $attachments = Attachment::where('email_id', $email->id)->get();

        $email->has_read = true;
        $email->save();

        return view('email', [
            'email' => $email,
            'selectedFolder' => $selectedFolder,
            'attachments' => $attachments,
            'selectedCredential' => ImapCredentials::find($linked_profile_id),
        ]);
    }

    public function archive($linked_profile_id = null, $folder = null, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
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

    public function read($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
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

    public function unread($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
            ->first();

        if ($email) {
            $email->has_read = false;
            $email->save();
        }

        return [
            'status' => 'success',
            'message' => 'Email marked as unread successfully.'
        ];
    }

    public function delete($linked_profile_id, $folder, $uuid)
    {
        Email::deleteEmail($uuid, $linked_profile_id);

        return [
            'status' => 'success',
            'message' => 'Email deleted successfully.'
        ];
    }

    public function star($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
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


    public function unstar($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
            ->first();

        if ($email) {
            $email->is_starred = false;
            $email->save();
        }

        return [
            'status' => 'success',
            'message' => 'Email starred successfully.'
        ];
    }

    public function readThread($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('linked_profile_id', $linked_profile_id)
            ->get();

        foreach ($emails as $threadEmail) {
            $threadEmail->has_read = true;
            $threadEmail->save();
        }

        return [
            'status' => 'success',
            'message' => 'Thread marked as read successfully.'
        ];
    }

    public function archiveThread($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('linked_profile_id', $linked_profile_id)
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

    public function deleteThread($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('linked_profile_id', $linked_profile_id)
            ->get();

        foreach ($emails as $threadEmail) {
            Email::deleteEmail($threadEmail->uuid, $threadEmail->linked_profile_id);
        }

        return [
            'status' => 'success',
            'message' => 'Thread deleted successfully.'
        ];
    }

    public function starThread($linked_profile_id, $folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('linked_profile_id', $linked_profile_id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('linked_profile_id', $linked_profile_id)
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
