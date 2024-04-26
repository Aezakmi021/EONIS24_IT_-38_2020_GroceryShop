<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user is an admin
        if (auth()->check() && auth()->user()->isAdmin === 1) {
            // User is an admin, allow access to the requested route
            return $next($request);
        }

        // User is not an admin, redirect or respond with an error
        return response()->view('errors.forbidden', [], 403);    }
}
