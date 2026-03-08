<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotifyHelper
{
    public static function send($title, $message, $url)
    {
        if (config('app.ntfy.enabled') !== true) {
            return true;
        }

        // Make a post request to compoments.lucasvanbriemen.nl/notify with the title, message and url as body
        Http::post('https://components.lucasvanbriemen.nl/api/notify', [
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'image' => 'https://commons.wikimedia.org/wiki/Special:FilePath/GoldenGateBridge.jpg',
        ])->throw();
    }
}
