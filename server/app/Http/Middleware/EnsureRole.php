<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (! in_array($request->user()->Role, $roles, true)) {
            return response()->json([
                'error' => 'Forbidden',
            ], 403);
        }

        return $next($request);
    }
}
