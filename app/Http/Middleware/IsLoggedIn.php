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
        $cookieFile = tempnam(sys_get_temp_dir(), 'cookie');

// Step 1: Get CSRF cookie
        $ch = curl_init('https://login.lucasvanbriemen.nl/sanctum/csrf-cookie');
        curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        ]);
        curl_exec($ch);
        curl_close($ch);

// Step 2: Request /api/user with cookies
        $ch = curl_init('https://login.lucasvanbriemen.nl/api/user');
        curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        // Optionally send the X-XSRF-TOKEN if Laravel's middleware expects it
        // 'X-XSRF-TOKEN: ' . $xsrfTokenValue (decoded from cookie)
        ]
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        unlink($cookieFile);


        if ($httpCode === 200) {
            return $next($request);
        }


        dd('You are not logged in. Please log in to continue.', $response, $httpCode);


        return redirect()->away('https://login.lucasvanbriemen.nl');
    }

}
