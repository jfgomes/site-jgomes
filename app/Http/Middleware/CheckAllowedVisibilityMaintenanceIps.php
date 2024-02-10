<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAllowedVisibilityMaintenanceIps
{
    public function handle(Request $request, Closure $next)
    {
        // Checks if the application is in maintenance mode
        if (app()->isDownForMaintenance())
        {
            // Checks if the IP is in the allowed list
            if ($this->isAllowedIp($request->ip()))
            {
                return $next($request);
            }

            // Returns a custom response to indicate that the application is under maintenance
            $message = 'The application is undergoing maintenance!';
            return response()->view('maintenance', ['message' => $message], 503);
        }
    }

    private function isAllowedIp($ip): bool
    {
        // Add the logic here to check if the IP is allowed
        return $ip == env('APP_MAINTENANCE_ALLOWED_IP');
    }
}
