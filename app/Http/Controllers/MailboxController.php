<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;

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
            $folder = $this->client->getFolder($folder);
        } else {
            $folder = $this->client->getFolder($this->DEFAULT_FOLDER);
        }


        $messages = [];
        if ($folder) {
            $messages = $folder->messages()->all()->get();
        }

        return view('index', [
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
