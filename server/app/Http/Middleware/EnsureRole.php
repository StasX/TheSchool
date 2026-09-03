<?php
namespace App\Http\Middleware;

use App\Models\Administrator;
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
        $administrator = $request->user();

        if (! $administrator instanceof Administrator) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

        if (! in_array($administrator->Role, $roles, true)) {
            return response()->json([
                'error' => 'Forbidden',
            ], 403);
        }

        return $next($request);
    }
}
