<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class MaintenancePermission
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
        $avoidable_maintenance_routes = config('permissionRoutes.'.ROUTE_TYPE_AVOIDABLE_MAINTENANCE);
        if($is_under_maintenance==UNDER_MAINTENANCE_MODE_ACTIVE && !$auth && !in_array($route_name, $avoidable_maintenance_routes)){
            throw new UnauthorizedException(ROUTE_REDIRECT_TO_UNDER_MAINTENANCE,401);
        }
        return $next($request);
    }
}
