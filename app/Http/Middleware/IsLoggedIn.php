<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Rebuild cookie header from original request
        $cookieJar = implode('; ', array_map(
            fn($k, $v) => "$k=$v",
            array_keys($request->cookies->all()),
            $request->cookies->all()
        ));


        var_dump('Checking if user is logged in', $cookieJar);

        $laravelSession = $_COOKIE['auth_token'] ?? null;
        var_dump('Laravel session cookie', $laravelSession);

        $ch = curl_init('https://login.lucasvanbriemen.nl/api/user/token/' . $laravelSession);
        curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Content-Type: application/json',
            "Cookie: $laravelSession"
        ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            dd('User is logged in', $response, $httpCode);
            return $next($request);
        }

        var_dump('You are not logged in. Please log in to continue.', $response, $httpCode);
    }
}
