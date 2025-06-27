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
use App\Models\Profile;
use App\Models\Tag;

class MailboxController extends Controller
{
    protected $DEFAULT_FOLDER = 'inbox';

    public function index($linked_profile_id = null, $folder = null)
    {
        $selectedFolder = $folder ?: $this->DEFAULT_FOLDER;

        // If a users has not an profile setup, lead them to the account page
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $selectedFolder = Folder::where('path', $selectedFolder)
            ->where('profile_id', $profile->id)
            ->first();

        $emails = Email::getEmails($selectedFolder, $profile);

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

        $tags = Tag::where('profile_id', $profile->id)->get();

        return view('overview', [
            'emailThreads' => $emailThreads,
            'selectedFolder' => $selectedFolder,
            'selectedProfile' => $profile,
            'tags' => $tags,
            'attachments' => []
        ]);
    }

    public function show($linked_profile_id, $folder, $uuid)
    {
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        if (!$profile) {
            return redirect('/'); // Redirect to home if profile not found
        }

        $tags = Tag::where('profile_id', $profile->id)->get();

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
            ->first();

        if (!$email) {
            return redirect('/');
        }

        $selectedFolder = Folder::where('path', $folder)
            ->where('profile_id', $profile->id)
            ->first();

        $attachments = Attachment::where('email_id', $email->id)->get();

        $email->has_read = true;
        $email->save();

        return view('email', [
            'email' => $email,
            'selectedFolder' => $selectedFolder,
            'attachments' => $attachments,
            'selectedProfile' => $profile,
            'tags' => $tags,
        ]);
    }

    public function archive($linked_profile_id = null, $folder = null, $uuid)
    {
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
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
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
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
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
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
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        Email::deleteEmail($uuid, $profile->id);

        return [
            'status' => 'success',
            'message' => 'Email deleted successfully.'
        ];
    }

    public function tag($linked_profile_id, $folder, $uuid)
    {
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
            ->first();

        if (!$email) {
            return [
                'status' => 'error',
                'message' => 'Email not found.'
            ];
        }

        $tagId = request()->input('tag_id');

        if ($tagId) {
            $email->tag_id = $tagId;
            $email->save();

            return [
                'status' => 'success',
                'message' => 'Email tagged successfully.'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Tag ID is required.'
        ];
    }

    public function star($linked_profile_id, $folder, $uuid)
    {
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
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
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
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
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('profile_id', $profile->id)
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
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('profile_id', $profile->id)
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
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('profile_id', $profile->id)
            ->get();

        foreach ($emails as $threadEmail) {
            Email::deleteEmail($threadEmail->uuid, $threadEmail->profile_id);
        }

        return [
            'status' => 'success',
            'message' => 'Thread deleted successfully.'
        ];
    }

    public function starThread($linked_profile_id, $folder, $uuid)
    {
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
            ->first();

        $emails = Email::where('sender_email', $email->sender_email)
            ->where('subject', $email->subject)
            ->where('folder_id', $email->folder_id)
            ->where('profile_id', $profile->id)
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
