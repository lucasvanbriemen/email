<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;

class MailboxController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = Client::account('default');
        $this->client->connect();
    }

    public function index()
    {
        $folders = $this->client->getFolders(false);

        $messages = [];

        $folders->each(function ($folder) use (&$messages) {
            $messages[] = $folder->messages()->all()->get();
        });

        return view('index', [
            'folders' => $folders,
            'messages' => $messages,
        ]);
    }

    public function show($uid)
    {
        $folder = $this->client->getFolder('INBOX');
        $message = $folder->messages()->getMessage($uid);


        if ($message) {
            return view('mail', [
                'message' => $message,
            ]);
        }
    }
}
