<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Folder;
use App\Models\Attachment;
use App\Models\User;
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

        if (!$selectedFolder) {
            // Redirect to the default folder if the selected folder does not exist
            return redirect()->route('mailbox.overview', [
                'linked_profile_id' => $linked_profile_id,
                'folder' => $this->DEFAULT_FOLDER
            ]);
        }

        $response = $this->getListingHTML($linked_profile_id, $selectedFolder->path);
        $data = $response->getData(true);  // Convert to array
        $listingHTML = $data['html'];

        // Use the threaded total count from the listing header
        $totalEmailCount = $data['header']['total_email_count'] ?? 0;

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

        $offset = $page * 50; // parent threads offset

        // If a users has not an profile setup, lead them to the account page
        $profile = Profile::linkedProfileIdToProfile($linked_profile_id);

        $selectedFolder = Folder::where('path', $selectedFolder)
            ->where('profile_id', $profile->id)
            ->first();

        // Build a base query similar to Email::getEmails but without limit so we can group into threads
        $query = Email::where('profile_id', $profile->id);

        if (!in_array($selectedFolder->path, Email::$customViewFolders)) {
            $query->where('folder_id', $selectedFolder->id);
            $query->where('is_archived', false);
        }

        if ($selectedFolder->path == 'trash') {
            $query->where('is_deleted', true);
        }

        if ($selectedFolder->path == 'spam') {
            $query->where('is_deleted', false)
                ->where('profile_id', '-1');
        }

        if ($selectedFolder->path == 'stared') {
            $query->where('is_starred', true);
        }

        // Only fetch fields needed for listing to avoid loading large html bodies
        $allEmails = $query
            ->select(['id','uuid','subject','sent_at','has_read','is_archived','is_starred','is_deleted','folder_id','profile_id','sender_id','sender_name'])
            ->orderBy('sent_at', 'desc')
            ->get();

        // Group emails into threads efficiently:
        // - Attach when (same sender AND exact same subject), OR
        // - Attach when subject is a reply to the thread's base subject (like Gmail's conversation view)
        $threads = [];
        $exactKeyIndex = []; // key: sender_email||subject_lower => thread index
        $baseKeyIndex = [];  // key: base_subject_lower => thread index (for replies only)

        foreach ($allEmails as $email) {
            $sender = (string)($email->sender->email ?? '');
            $subject = (string)($email->subject ?? '');
            $subjectNorm = $this->normalizeSubjectText($subject);
            $subjectLower = mb_strtolower($subjectNorm);
            $baseLower = mb_strtolower($this->baseSubject($subjectNorm));

            $exactKey = $sender . '||' . $subjectLower;
            $isReply = $this->hasReplyPrefix($subjectNorm);

            $attached = false;

            // Case 1: same sender + exact subject
            if (isset($exactKeyIndex[$exactKey])) {
                $threads[$exactKeyIndex[$exactKey]]['children'][] = $email;
                $attached = true;
            }

            // Case 2: reply to existing base subject (ignore sender)
            if (!$attached && $isReply && isset($baseKeyIndex[$baseLower])) {
                $threads[$baseKeyIndex[$baseLower]]['children'][] = $email;
                $attached = true;
            }

            if (!$attached) {
                // Start a new thread with this email as parent
                $threads[] = [
                    'parent' => $email,
                    'children' => [],
                ];
                $idx = count($threads) - 1;
                $exactKeyIndex[$exactKey] = $idx;
                // Map base subject for replies to attach later
                if (!isset($baseKeyIndex[$baseLower])) {
                    $baseKeyIndex[$baseLower] = $idx;
                }
            }
        }

        $totalThreads = count($threads);
        $pageThreads = array_slice($threads, $offset, 50);

        $html = '';
        foreach ($pageThreads as $thread) {
            $html .= view('email_thread', [
                'parent' => $thread['parent'],
                'children' => $thread['children'],
                'linked_profile_id' => $linked_profile_id,
                'folderPath' => $selectedFolder->path,
            ])->render();
        }

        // If no emails are found, show a message
        if (empty($html)) {
            $html = view('no_emails')->render();
        }

        $current_max = min($offset + 50, $totalThreads);

        return response()->json([
            'html' => $html,
            'header' => [
                'folder' => $selectedFolder->name,
                // Show total amount of parent threads
                'total_email_count' => $totalThreads,
                'previous_page' => $page > 0 ? $page - 1 : null,
                'next_page' => ($page + 1) * 50 < $totalThreads ? $page + 1 : null,
                'current_min' => $totalThreads === 0 ? 0 : $offset + 1,
                'current_max' => $current_max,
            ]
        ]);
    }

    // --- Lightweight subject helpers for fast threading ---
    private function normalizeSubjectText(string $subject): string
    {
        $subject = trim($subject);
        // Collapse multiple spaces/tabs
        $subject = preg_replace('/\s+/u', ' ', $subject ?? '') ?? '';
        return $subject;
    }

    private function baseSubject(string $subject): string
    {
        // Remove common reply/forward prefixes like: Re:, RE:, Fwd:, FW:, Re[2]:, etc. (possibly repeated)
        $s = $subject;
        // Keep stripping while it matches
        while (preg_match('/^\s*((re|fw|fwd)(\[\d+\])?\s*:)\s*/i', $s)) {
            $s = preg_replace('/^\s*((re|fw|fwd)(\[\d+\])?\s*:)\s*/i', '', $s);
        }
        return trim($s);
    }

    private function hasReplyPrefix(string $subject): bool
    {
        return (bool)preg_match('/^\s*(re|fw|fwd)(\[\d+\])?\s*:/i', $subject);
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

        // Build relevant messages (children) list based on new subject+sender/reply rule
        $threadChildren = [];
        if ($email && $selectedFolder) {
            $baseQuery = Email::where('profile_id', $profile->id)
                ->where('folder_id', $selectedFolder->id)
                ->where('is_archived', false)
                ->select(['id','uuid','subject','sent_at','has_read','is_archived','is_starred','is_deleted','folder_id','profile_id','sender_id','sender_name'])
                ->orderBy('sent_at', 'desc');

            $allCandidates = $baseQuery->get();

            $seedSubjectNorm = $this->normalizeSubjectText((string)($email->subject ?? ''));
            $seedSubjectLower = mb_strtolower($seedSubjectNorm);
            $seedBaseLower = mb_strtolower($this->baseSubject($seedSubjectNorm));

            foreach ($allCandidates as $candidate) {
                if ($candidate->id === $email->id) { continue; }

                $candSubjectNorm = $this->normalizeSubjectText((string)($candidate->subject ?? ''));
                $candSubjectLower = mb_strtolower($candSubjectNorm);
                $candBaseLower = mb_strtolower($this->baseSubject($candSubjectNorm));
                $candIsReply = $this->hasReplyPrefix($candSubjectNorm);

                // Case 1: same sender + exact subject
                if ((string)($candidate->sender?->email ?? '') === (string)($email->sender?->email ?? '') && $candSubjectLower === $seedSubjectLower) {
                    $threadChildren[] = $candidate;
                    continue;
                }

                // Case 2: reply to same base subject (ignore sender)
                if ($candIsReply && $candBaseLower === $seedBaseLower) {
                    $threadChildren[] = $candidate;
                    continue;
                }
            }
        }

        return view('email_data', [
            'email' => $email,
            'selectedFolder' => $selectedFolder,
            'attachments' => $attachments,
            'selectedProfile' => $profile,
            'tags' => $tags,
            'standalone' => $standalone,
            'threadChildren' => $threadChildren,
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

        $tagId = request()->json('tag_id');

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

        $thread = $this->findSimilarThreadEmails($email, $profile);

        foreach ($thread as $threadEmail) {
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

        $thread = $this->findSimilarThreadEmails($email, $profile);

        foreach ($thread as $threadEmail) {
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

        $thread = $this->findSimilarThreadEmails($email, $profile);

        foreach ($thread as $threadEmail) {
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

        $thread = $this->findSimilarThreadEmails($email, $profile);

        foreach ($thread as $threadEmail) {
            $threadEmail->is_starred = true;
            $threadEmail->save();
        }

        return [
            'status' => 'success',
            'message' => 'Thread starred successfully.'
        ];
    }

    private function findSimilarThreadEmails($seedEmail, $profile)
    {
        if (!$seedEmail) {
            return collect();
        }

        // Collect thread emails using same rule as listing
        $baseQuery = Email::where('profile_id', $profile->id)
            ->where('folder_id', $seedEmail->folder_id)
            ->where('is_archived', false)
            ->select(['id','uuid','subject','sent_at','has_read','is_archived','is_starred','is_deleted','folder_id','profile_id','sender_id','sender_name'])
            ->orderBy('sent_at', 'desc');

        $candidates = $baseQuery->get();

        $seedSubjectNorm = $this->normalizeSubjectText((string)($seedEmail->subject ?? ''));
        $seedSubjectLower = mb_strtolower($seedSubjectNorm);
        $seedBaseLower = mb_strtolower($this->baseSubject($seedSubjectNorm));

        $thread = collect([$seedEmail]);
        foreach ($candidates as $candidate) {
            if ($candidate->id === $seedEmail->id) { continue; }

            $candSubjectNorm = $this->normalizeSubjectText((string)($candidate->subject ?? ''));
            $candSubjectLower = mb_strtolower($candSubjectNorm);
            $candBaseLower = mb_strtolower($this->baseSubject($candSubjectNorm));
            $candIsReply = $this->hasReplyPrefix($candSubjectNorm);

            // Case 1: same sender + exact subject
            if ((string)($candidate->sender?->email ?? '') === (string)($seedEmail->sender?->email ?? '') && $candSubjectLower === $seedSubjectLower) {
                $thread->push($candidate);
                continue;
            }

            // Case 2: reply to same base subject (ignore sender)
            if ($candIsReply && $candBaseLower === $seedBaseLower) {
                $thread->push($candidate);
                continue;
            }
        }

        return $thread;
    }
}
