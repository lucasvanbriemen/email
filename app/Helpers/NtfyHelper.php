<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class NtfyHelper
{
    public static function sendNofication($title, $message, $url)
    {

        if (config('app.ntfy.enabled') !== true) {
            return true;
        }

        $result = file_get_contents('https://ntfy.sh/lukaas_test', false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => [
                    'Content-Type: text/plain',
                    'Title: ' . $title,
                    'Click: ' . $url,
                    'Priority: 5',
                ],
                'content' => (string) $message
            ]
        ]));

        Log::info('Ntfy response', ['response' => $result]);

        return $result;
    }
}
