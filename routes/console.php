<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Webklex\IMAP\Facades\Client;

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

    foreach ($messages as $message) {
        if (!in_array($message->getUid(), $dbMessages)) {
            $newMessages[] = [
                'subject' => $message->getSubject(),
                'from' => $message->getFrom()[0]->personal ?? $message->getFrom()[0]->mail ?? null,
                'sent_at' => date($message->getDate()),
                'has_read' => $message->getFlags()->has('seen'),
                'uid' => $message->getUid(),
            ];
        }
    }

    foreach ($newMessages as $message) {
        file_get_contents('https://ntfy.sh/lukaas_test', false, stream_context_create([
            'http' => [
                'method' => 'POST', // PUT also works
                'header' => 'Content-Type: text/plain',
                'content' => $message['subject'] . ' - ' . $message['from']
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
