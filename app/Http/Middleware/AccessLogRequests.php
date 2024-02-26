<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class AccessLogRequests
{
    public function handle($request, Closure $next)
    {
        if (app()->environment('local'))
         {
            // Registry the log request
            Log::channel('access')
                ->info('New request: ' . $request->fullUrl());
        }
        return $next($request);
    }
}
