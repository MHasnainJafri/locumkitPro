<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (class_exists("\Barryvdh\Debugbar\Facades\Debugbar") && is_a("\Barryvdh\Debugbar\Facades\Debugbar", Facade::class, true)) {
            \Barryvdh\Debugbar\Facades\Debugbar::disable();
        }

        $request->headers->set('Accept', 'application/json');

        // Get the response
        $response = $next($request);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
