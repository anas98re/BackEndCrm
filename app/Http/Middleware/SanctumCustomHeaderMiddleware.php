<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanctumCustomHeaderMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('AuthToken');

        if ($token) {
            $request->headers->set('Authorization',$token);
        }

        return $next($request);
    }
}
