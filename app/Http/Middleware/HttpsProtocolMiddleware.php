<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

class HttpsProtocolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(app()->environment('production')) {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
