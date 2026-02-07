<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $expectedToken = env('EMAIL_API_AUTH_TOKEN');

        // If no token is configured, allow the request
        if (!$expectedToken) {
            return $next($request);
        }

        // If token is required but not provided, or doesn't match
        if (!$token || $token !== $expectedToken) {
            return response()->json([
                'error' => 'Invalid or missing authentication token'
            ], 401);
        }

        return $next($request);
    }
}
