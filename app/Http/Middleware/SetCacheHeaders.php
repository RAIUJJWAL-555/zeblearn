<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     * Sets appropriate Cache-Control and Vary headers on dynamic responses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Ensure dynamic HTML responses require revalidation so asset updates are picked up immediately
        if ($request->isMethod('GET')) {
            $response->headers->set('Cache-Control', 'no-cache, private, must-revalidate');
        }

        if (! $response->headers->has('Vary')) {
            $response->headers->set('Vary', 'Accept-Encoding');
        }

        return $response;
    }
}
