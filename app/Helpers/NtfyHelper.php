<?php

namespace App\Helpers;

class NtfyHelper
{
    public static function sendNofication($title, $message, $url)
    {
        $result = file_get_contents('https://ntfy.sh/lukaas_test', false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => [
                    'Content-Type: text/plain',
                    'Title: ' . $title,
                    'Click: ' . $url,
                ],
                'content' => (string) $message
            ]
        ]));

        return $result;
    }
}
