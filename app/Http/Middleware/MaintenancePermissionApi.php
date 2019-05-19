<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MaintenancePermissionApi
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
        $auth = Auth::check();
        $is_under_maintenance = admin_settings('maintenance_mode');
        $route_name = $request->route()->getName();
        $avoidable_maintenance_routes = config('routeApi.'.ROUTE_TYPE_AVOIDABLE_MAINTENANCE);
        if($is_under_maintenance==UNDER_MAINTENANCE_MODE_ACTIVE && !$auth && !in_array($route_name, $avoidable_maintenance_routes)){
            return response()->json([401 =>api_permission(ROUTE_REDIRECT_TO_UNDER_MAINTENANCE)]);
        }
        return $next($request);
    }
}
