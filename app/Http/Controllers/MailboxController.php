<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use App\Models\Email;
use App\Models\Folder;
use App\Models\Attachment;

class MailboxController extends Controller
{
    protected $client;
    protected $DEFAULT_FOLDER = 'INBOX';

    public function index($folder = null)
    {
        $selectedFolder = $folder ?: $this->DEFAULT_FOLDER;

        $folder = Folder::where('name', $selectedFolder)
            ->where('user_id', auth()->id())
            ->first();

        $emails = Email::getEmails($folder);

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
        ]);
    }

    public function show($folder, $uuid)
    {
        $email = Email::where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->first();

        $selectedFolder = $email->folder->name;

        $attachments = Attachment::where('email_id', $email->id)->get();

        $email->has_read = true;
        $email->save();

        return view('email', [
            'email' => $email,
            'selectedFolder' => $selectedFolder,
            'attachments' => $attachments,
        ]);
    }

    public function archive($folder, $uuid)
    {
        $email = Email::where('uid', $uuid)
            ->where('user_id', auth()->id())
            ->first();

        $folder = $email->folder->name;

        if ($email) {
            $email->is_archived = true;
            $email->save();
        }

        return redirect()->route('mailbox.folder', ['folder' => $folder]);
    }

    public function delete($folder, $uuid)
    {
        Email::deleteEmail( $uuid);

        return redirect()->route('mailbox.folder', ['folder' => $folder]);
    }
}
