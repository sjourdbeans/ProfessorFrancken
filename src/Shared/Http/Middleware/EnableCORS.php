<?php

declare(strict_types=1);

namespace Francken\Shared\Http\Middleware;

use Closure;

final class EnableCORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->segment(2) === "pluimpje" || $request->segment(1) === "api") {
            return $response->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        return $response;
    }
}