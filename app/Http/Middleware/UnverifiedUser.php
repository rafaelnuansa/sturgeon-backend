<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnverifiedUser
{
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->verified) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a verified user.',
            ], 403);
        }

        return $next($request);
    }
}
