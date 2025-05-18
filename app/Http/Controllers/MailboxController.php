<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;

class MailboxController extends Controller
{
    //
    public function index()
    {
        $client = Client::account('default');
        $client->connect();

        $folders = $client->getFolders(false);

        $folders->each(function ($folder) {
            echo $folder->path . '<br>';

            $messages = $folder->messages()->all()->get();
            $messages->each(function ($message) {
                echo $message->getSubject() . '<br>';
            });
        });

        return view('index');
    }
}
