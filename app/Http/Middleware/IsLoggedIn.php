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


        dump('Checking if user is logged in', $cookieJar);

        // $laravelSession = $_COOKIE['laravel_session'] ?? null;
        $laravelSession = "eyJpdiI6IityVml3L0xKSUMzaHdXclp1bXN6ZXc9PSIsInZhbHVlIjoibFd4ejZ3SUFUTnVrc1l6OXFkRkhvT1JUaG90QSt1bW9BYTFuWkhENGFFemxlelJHZ3NUR2RuQ2lJQWFid3ZaeGRWbjBPT0JpZ2NBZ3dlejhjMEM3L3JybFhTeVBIYTd3dWhMQWx0RjhOZ2krZHdFM3ZRc3lWSXpWallhMDZsbUoiLCJtYWMiOiI1ZDA2ZTk3YzQ4OTI2OGYwZjE1ZTQzMTBjYjEzOTQ3Njg1ODFhYzRkODUwODk1NzYwMDZlMDMwMWI5MTYwZWYzIiwidGFnIjoiIn0%3D";
        dump('Laravel session cookie', $laravelSession);

        $ch = curl_init('https://login.lucasvanbriemen.nl/api/user');
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
            return $next($request);
        }

        dd('You are not logged in. Please log in to continue.', $response, $httpCode);
    }



}
