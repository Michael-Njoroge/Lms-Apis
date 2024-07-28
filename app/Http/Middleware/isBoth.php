<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isBoth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if($user->role !== 'admin' && $user->role !== 'instructor'){
            return response()->json([
                'status' => false,
                'code' => 401,
                'message' => 'Your dont have the required permissions'
            ]);
        }
        return $next($request);
    }
}
