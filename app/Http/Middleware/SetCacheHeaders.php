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

        // Enable transparent gzip compression for text/HTML responses in non-testing environments when supported by client
        if (! app()->environment('testing') && $request->isMethod('GET') && ! $response->headers->has('Content-Encoding')) {
            $acceptEncoding = (string) $request->header('Accept-Encoding', '');
            if (str_contains($acceptEncoding, 'gzip') && function_exists('gzencode')) {
                $contentType = (string) $response->headers->get('Content-Type', '');
                if (str_contains($contentType, 'text/html') || str_contains($contentType, 'application/json')) {
                    $content = $response->getContent();
                    if ($content && strlen($content) > 1024) {
                        $compressed = gzencode($content, 6);
                        if ($compressed !== false) {
                            $response->setContent($compressed);
                            $response->headers->set('Content-Encoding', 'gzip');
                            $response->headers->set('Content-Length', (string) strlen($compressed));
                        }
                    }
                }
            }
        }

        return $response;
    }
}
