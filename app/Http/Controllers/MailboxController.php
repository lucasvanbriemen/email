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

        $allEmails = $query->orderBy('sent_at', 'desc')->get();

        // Group emails into threads efficiently (same sender + >=90% similarity by token Jaccard)
        $threads = [];
        $threadsBySender = []; // sender_email => array of thread indices

        foreach ($allEmails as $email) {
            $sender = $email->sender_email;
            if (!isset($threadsBySender[$sender])) {
                $threadsBySender[$sender] = [];
            }

            $tokens = $this->tokenizeBody($this->normalizeBody($email->html_body ?? ''));
            if (empty($tokens)) {
                // If no tokens, start a new thread to avoid expensive comparisons
                $threads[] = [
                    'parent' => $email,
                    'children' => [],
                    'parent_tokens' => $tokens,
                ];
                $threadsBySender[$sender][] = count($threads) - 1;
                continue;
            }

            $attached = false;
            foreach ($threadsBySender[$sender] as $threadIndex) {
                $parentTokens = $threads[$threadIndex]['parent_tokens'];
                // Quick length filter: if token counts differ a lot, skip
                $countA = max(1, count($tokens));
                $countB = max(1, count($parentTokens));
                $lenRatio = min($countA, $countB) / max($countA, $countB);
                if ($lenRatio < 0.8) { // if very different, can't be 90% similar
                    continue;
                }

                $sim = $this->jaccardSimilarity($tokens, $parentTokens);
                if ($sim >= 0.90) {
                    $threads[$threadIndex]['children'][] = $email;
                    $attached = true;
                    break;
                }
            }

            if (!$attached) {
                // Create a new thread with this email as parent
                $threads[] = [
                    'parent' => $email,
                    'children' => [],
                    'parent_tokens' => $tokens,
                ];
                $threadsBySender[$sender][] = count($threads) - 1;
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

    private function normalizeBody(string $html): string
    {
        // Strip HTML, collapse whitespace, and limit to a reasonable size for comparison
        $text = strip_tags($html);
        $text = preg_replace('/\s+/u', ' ', $text ?? '') ?? '';
        $text = trim($text);
        // Limit to first 5000 chars to keep similar_text performant
        return mb_substr($text, 0, 5000);
    }

    private function tokenizeBody(string $text): array
    {
        // Lowercase, keep letters and numbers as tokens, remove very short tokens
        $text = mb_strtolower($text);
        // Split on non-alphanumeric
        $parts = preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) {
            return [];
        }
        $tokens = [];
        foreach ($parts as $p) {
            if (mb_strlen($p) >= 3) {
                $tokens[$p] = true; // use associative array as a set
            }
        }
        // Limit token set size to avoid heavy comparisons
        if (count($tokens) > 400) {
            $tokens = array_slice($tokens, 0, 400, true);
        }
        return $tokens;
    }

    private function jaccardSimilarity(array $setA, array $setB): float
    {
        if (empty($setA) && empty($setB)) {
            return 1.0;
        }
        if (empty($setA) || empty($setB)) {
            return 0.0;
        }
        $intersection = 0;
        $union = count($setA);
        foreach ($setB as $token => $_) {
            if (isset($setA[$token])) {
                $intersection++;
            } else {
                $union++;
            }
        }
        if ($union === 0) {
            return 0.0;
        }
        return $intersection / $union;
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
