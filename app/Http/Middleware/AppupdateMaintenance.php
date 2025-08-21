<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\MaintenanceSetting;

class AppupdateMaintenance
{
    public function handle($request, Closure $next)
    {
        $setting = MaintenanceSetting::first();

        if ($setting && $setting->maintenance_mode == 'on') {
            return response()->json([
                'status' => 503,
                'message' => 'Under Maintenance!',
            ], 503);
        }

        return $next($request);
    }
}
