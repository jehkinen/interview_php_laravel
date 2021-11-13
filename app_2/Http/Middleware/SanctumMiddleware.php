<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SanctumMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            Auth::setUser($user);
        }

        return $next($request);
    }
}
