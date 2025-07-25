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
        $laravelSession = $_COOKIE['auth_token'] ?? null;

        $ch = curl_init('https://login.lucasvanbriemen.nl/api/user/token/' . $laravelSession);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            dd($response);
            return $next($request);
        } else {
            // Redirect to login.lucasvanbriemen.nl, with the current URL as a query parameter
            return redirect('https://login.lucasvanbriemen.nl?redirect=' . urlencode($request->fullUrl()));
        }
    }
}
