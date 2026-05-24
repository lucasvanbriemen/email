<?php

namespace App\Helpers;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;

class ApnsHelper
{
    public static function sendToUser(int $userId, string $title, string $body, array $extra = []): void
    {
        if (config('app.apns.enabled') !== true) {
            return;
        }

        $tokens = DeviceToken::where('user_id', $userId)->get();
        if ($tokens->isEmpty()) {
            return;
        }

        $payload = json_encode([
            'aps' => [
                'alert' => ['title' => $title, 'body' => $body],
                'sound' => 'default',
            ],
        ] + $extra);

        $certPath = config('app.apns.cert_path');
        $certPassword = config('app.apns.cert_password');
        $topic = config('app.apns.topic');
        $host = config('app.apns.environment') === 'production'
            ? 'https://api.push.apple.com'
            : 'https://api.development.push.apple.com';

        if (!$certPath || !file_exists($certPath)) {
            Log::warning('APNs cert not found at ' . ($certPath ?: '<unset>'));
            return;
        }

        foreach ($tokens as $token) {
            self::sendOne($host, $token, $topic, $certPath, $certPassword, $payload);
        }
    }

    private static function sendOne(string $host, DeviceToken $token, string $topic, string $certPath, ?string $certPassword, string $payload): void
    {
        $ch = curl_init("$host/3/device/{$token->token}");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_HTTPHEADER => [
                "apns-topic: $topic",
                'apns-push-type: alert',
                'Content-Type: application/json',
            ],
            CURLOPT_SSLCERT => $certPath,
            CURLOPT_SSLCERTPASSWD => $certPassword ?: '',
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200) {
            return;
        }

        if ($httpCode === 410) {
            $token->delete();
            return;
        }

        Log::warning("APNs push failed (HTTP $httpCode) for token {$token->id}: " . ($curlError ?: $response));
    }
}
