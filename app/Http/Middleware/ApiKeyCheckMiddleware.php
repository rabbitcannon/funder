<?php

namespace App\Http\Middleware;

use Closure;

class ApiKeyCheckMiddleware
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
        $presented = $request->get('apikey');
        if(!$presented || $presented != config('app.api_key'))
        { return response('api key needed', 401); }
        return $next($request);
    }
}
