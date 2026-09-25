<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = config('app.basic_auth.username');
        $password = config('app.basic_auth.password');

        $getUsername = $request->getUser();
        $getPassword = $request->getPassword();

        if ($getUsername !== $username || $getPassword !== $password) {
            return response('Unauthorized', 401);
        }

        return $next($request);
    }
}
