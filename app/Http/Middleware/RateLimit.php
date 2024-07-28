<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class RateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $limit, $duration)
    {
        $key = $request->ip();
        
        $rateLimit = RateLimiter::for($key, function () use ($limit, $duration, $key) {
            return Limit::perMinutes($duration, $limit)->by($key);
        });

        if (!RateLimiter::tooManyAttempts($key, $limit)) {
            RateLimiter::hit($key);
            return $next($request);
        }

        $retryAfter = RateLimiter::availableIn($key);
        return response()->json([
            'status' => false,
            'code' => 429,
            'message' => 'Too many attempts, you are allowed ' . $limit . ' requests per ' . $duration/60 .' minute(s). Please try again after ' . $retryAfter . ' seconds.',
        ], 429);
    }
}
