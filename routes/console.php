<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {
    // Get All emails
    $client = Client::account('default');
    $client->connect();
    $folder = $client->getFolder('INBOX');
    $messages = $folder->messages()->all()->get();

    // Compare with DB
    $dbMessages = DB::table('emails')->pluck('uid')->toArray();
    $newMessages = [];

    $folderName = $folder->name;
    foreach ($messages as $message) {
        if (!in_array($message->getUid(), $dbMessages)) {
            $newMessages[] = [
                'subject' => $message->getSubject(),
                'from' => $message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? null,
                'sent_at' => date($message->getDate()),
                'has_read' => $message->getFlags()->has('seen'),
                'uid' => $message->getUid(),
                'parrent_folder' => $folderName,
            ];
        }
    }

    foreach ($newMessages as $message) {
        file_get_contents('https://ntfy.sh/lukaas_test', false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => [
                    'Content-Type: text/plain',
                    'Title: ' . $message['from'],
                    'Click: ' . 'https://email.lucasvanbriemen.nl/folder/' . $message['parrent_folder'] . '/' . $message['uid'],
                ],
                'content' => $message['subject']
            ]
        ]));

        DB::table('emails')->insert([
            'subject' => $message['subject'],
            'from' => $message['from'],
            'sent_at' => $message['sent_at'],
            'has_read' => $message['has_read'],
            'uid' => $message['uid'],
        ]);
    }
})->everyMinute();
