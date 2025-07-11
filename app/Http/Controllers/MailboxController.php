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

        $response = $this->getListingHTML($linked_profile_id, $selectedFolder->path);
        $data = $response->getData(true);  // Convert to array
        $listingHTML = $data['html'];

        $totalEmailCount = Email::where('folder_id', $selectedFolder->id)
            ->where('profile_id', $profile->id)
            ->count();

        return view('overview', [
            'listingHTML' => $listingHTML,

            'selectedFolder' => $selectedFolder,
            'totalEmailCount' => $totalEmailCount,
            'currentMin' => $data['header']['current_min'] ?? 0,
            'currentMax' => $data['header']['current_max'] ?? 0,
            'previousPage' => $data['header']['previous_page'] ?? null,
            'nextPage' => $data['header']['next_page'] ?? null
        ]);
    }

    public function getListingHTML($linked_profile_id = null, $folder = null, $page = 0)
    {
        $selectedFolder = $folder ?: $this->DEFAULT_FOLDER;

        $offset = $page * 50;

        // If a users has not an profile setup, lead them to the account page
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $selectedFolder = Folder::where('path', $selectedFolder)
            ->where('profile_id', $profile->id)
            ->first();

        $emails = Email::getEmails($selectedFolder, $profile, $offset);

        $html = '';

        foreach ($emails as $email) {
            if (!$email->subject) {
                $email->subject = 'No Subject';
            }

            $pathToEmail = route('mailbox.folder.mail', [
                'linked_profile_id' => $linked_profile_id,
                'folder' => $selectedFolder->path,
                'uuid' => $email->uuid,
            ]);

            $html .= view('email_listing', [
                'email' => $email,
                'pathToEmail' => $pathToEmail
            ])->render();
        }

        // If no emails are found, show a message
        if (empty($html)) {
            $html = view('no_emails')->render();
        }

        $current_max = $offset + 50;
        if (
            $current_max > Email::where('folder_id', $selectedFolder->id)
            ->where('profile_id', $profile->id)->count()
        )
        {
            $current_max = Email::where('folder_id', $selectedFolder->id)
                ->where('profile_id', $profile->id)->count();
        }

        return response()->json([
            'html' => $html,
            'header' => [
                'folder' => $selectedFolder->name,
                'total_email_count' => Email::where('folder_id', $selectedFolder->id)
                    ->where('profile_id', $profile->id)
                    ->count(),
                'previous_page' => $page > 0 ? $page - 1 : null,
                'next_page' => Email::where('folder_id', $selectedFolder->id)
                    ->where('profile_id', $profile->id)
                    ->count() > ($page + 1) * 50 ? $page + 1 : null,
                'current_min' => $offset,
                'current_max' => $current_max,
            ]
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

        // If the contents of the email are empty and there is an attachment that has end with .html, we will show the HTML content of the email or .txt
        if (empty($email->html_body) && $attachments->isNotEmpty()) {
            foreach ($attachments as $attachment) {
                if (str_ends_with($attachment->path, '.html') || str_ends_with($attachment->path, '.txt')) {
                    $email->html_body = file_get_contents($attachment->path);
                    break;
                }
            }
        }

        $email->has_read = true;
        $email->save();

        return view('email', [
            'email' => $email,
            'selectedFolder' => $selectedFolder,
            'attachments' => $attachments,
            'selectedProfile' => $profile,
            'tags' => $tags,
        ])->render();
    }

    public function showHtml($linked_profile_id, $folder, $uuid, $standalone = false)
    {
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $tags = Tag::where('profile_id', $profile->id)->get();

        $email = Email::where('uuid', $uuid)
            ->where('profile_id', $profile->id)
            ->first();

        $selectedFolder = Folder::where('path', $folder)
            ->where('profile_id', $profile->id)
            ->first();

        $attachments = Attachment::where('email_id', $email->id)->get();

        $email->has_read = true;
        $email->save();

        return view('email_data', [
            'email' => $email,
            'selectedFolder' => $selectedFolder,
            'attachments' => $attachments,
            'selectedProfile' => $profile,
            'tags' => $tags,
            'standalone' => $standalone,
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
