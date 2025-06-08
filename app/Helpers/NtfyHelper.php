<?php

namespace App\Helpers;

class NtfyHelper
{
    public static function sendNofication($title, $message, $url)
    {

        if (!config("ntfy.enabled")) {
            return;
        }

        $result = file_get_contents(config("ntfy.url"), false, stream_context_create([
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
