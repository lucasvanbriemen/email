<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use App\Models\Email;

class MailboxController extends Controller
{
    protected $client;
    protected $DEFAULT_FOLDER = 'INBOX';

    public function __construct()
    {
        $this->client = Client::account('default');
        $this->client->connect();
    }

    public function index($folder = null)
    {
        if ($folder) {
            $selectedFolder = $folder;
            $folder = $this->client->getFolder($folder);
        } else {
            $selectedFolder = $this->DEFAULT_FOLDER;
            $folder = $this->client->getFolder($this->DEFAULT_FOLDER);
        }

        $messages = [];
        $rawMessages = $folder->messages()->all()->get();
        $sortedMessages = $rawMessages->sortByDesc(function ($message) {
            return $message->getDate();
        });

        $messages = $sortedMessages->map(function ($message) {
            return [
                'subject' => $message->getSubject(),
                'from' => $message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? null,
                'sent_at' => $message->getDate()->first()->setTimezone(config('app.display_timezone'))->format('Y-m-d H:i:s'),
                'has_read' => $message->getFlags()->has('seen'),
                'uid' => $message->getUid(),
            ];
        });

        return view('index', [
            'messages' => $messages,
            'folder' => $folder,
            'selectedFolder' => $selectedFolder,
        ]);
    }

    public function show($folder, $uid)
    {
        $folder = $this->client->getFolder($folder);
        $message = $folder->messages()->getMessage($uid);

        // mark as read
        if ($message->getFlags()->has('seen') == false) {
            $message->setFlag(['Seen']);
        }

        if ($message) {
            return view('mail', [
                'message' => $message,
            ]);
        }
    }
}
