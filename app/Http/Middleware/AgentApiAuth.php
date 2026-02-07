<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $expectedToken = env('AGENT_TOKEN');

        if (!$expectedToken) {
            return $next($request);
        }

        if (!$token || $token !== $expectedToken) {
            return response()->json([
                'error' => 'Invalid or missing authentication token'
            ], 401);
        }

        return $next($request);
    }
}
