<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->verified) {
            // You can customize the response or redirect as needed.
            return response()->json([
                'success' => false,
                'error' => 'You must be verified to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
