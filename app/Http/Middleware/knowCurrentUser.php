<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class knowCurrentUser
{
    public function handle(Request $request, Closure $next)
    {
        $currentUser = auth('sanctum')->user();

        // If user is authenticated, attach user ID to request headers
        if ($currentUser) {
            $request->merge(['id_user' => $currentUser->id_user]);
        }
        return $next($request);
    }
}
