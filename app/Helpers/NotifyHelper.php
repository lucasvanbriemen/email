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

        try {
            Http::post('https://components.lucasvanbriemen.nl/api/notify', [
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'image' => 'https://email.lucasvanbriemen.nl/logo.png',
            ])->throw();
        } catch (\Exception $e) {
            Log::warning('Failed to send notification: ' . $e->getMessage());
        }
    }
}
