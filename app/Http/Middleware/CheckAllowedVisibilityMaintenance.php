<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CheckAllowedVisibilityMaintenance
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the application is in maintenance mode
        if (app()->isDownForMaintenance())
        {
            // Checks if allowed
            if ($this->isAllowed())
            {
                return $next($request);
            }

            // Returns a custom response to indicate that the application is under maintenance
            $message = 'The application is undergoing maintenance!';
            return response()->view('maintenance', ['message' => $message], 503);
        }

        return $next($request);
    }

    private function isAllowed(): bool
    {
        // Check if is allowed
        $conditionalFlag = env('APP_ROUTE_COOKIE_FLAG');
        return ($conditionalFlag && Cookie::has($conditionalFlag));
    }
}
