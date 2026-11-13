<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Verify that the user is authenticated
        if (! $user) {
            if ($request->expectsJson()) {
                abort(Response::HTTP_FORBIDDEN, 'Unauthorized access.');
            }

            return redirect()->guest(route('login'));
        }

        // 2. Reject suspended users from accessing protected LMS routes
        if (! $user->isActive()) {
            abort(Response::HTTP_FORBIDDEN, 'Your account is suspended. Please contact support.');
        }

        // 3. Verify role authorization (if specific roles are required)
        if (! empty($roles) && ! in_array($user->role, $roles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Access denied for your role.');
        }

        return $next($request);
    }
}
