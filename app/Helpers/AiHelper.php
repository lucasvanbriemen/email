<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class AiHelper
{
    public static function summarize($text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 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',
            'Content-Type' => 'application/json'
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
        'model' => 'deepseek/deepseek-chat-v3-0324:free',
        'messages' => [
        [
            'role' => 'user',
            'content' => $text
        ],
        ],
        ]);

        $result = $response->json();

        return $result;
    }
}
