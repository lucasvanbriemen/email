<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use App\Models\Email;
use App\Models\Folder;

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

        $emails = Email::where('folder_id', $folder->id)
            ->where('user_id', auth()->id())
            ->orderBy('sent_at', 'desc')
            ->get();

        return view('index', [
            'emails' => $emails,
            'folder' => $folder,
            'selectedFolder' => $selectedFolder,
        ]);
    }

    public function show($folder, $uid)
    {
        $email = Email::where('uid', $uid)
            ->where('user_id', auth()->id())
            ->first();

        $email->has_read = true;
        $email->save();

        return view('mail', [
            'email' => $email,
        ]);
    }
}
