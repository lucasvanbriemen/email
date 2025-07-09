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

        $context = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => [
                'Content-Type: text/plain',
                'Title: ' . $title,
                'Click: ' . $url,
                'Priority: 5',
            ],
            'content' => (string) $message,
            'ignore_errors' => true,
        ]
        ]);

        $response = file_get_contents('https://ntfy.sh/lukaas_test', false, $context);

        if (!isset($http_response_header[0])) {
            throw new \RuntimeException('Ntfy send failed: No response headers');
        }

        if (!preg_match('#HTTP/\d+\.\d+\s+(\d+)#', $http_response_header[0], $matches)) {
            throw new \RuntimeException('Ntfy send failed: Invalid HTTP response');
        }

        $statusCode = (int) $matches[1];
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \RuntimeException("Ntfy send failed: HTTP $statusCode - $response");
        }

        return true;
    }
}
