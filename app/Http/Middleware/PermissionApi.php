<?php

namespace App\Http\Middleware;

use Closure;

class PermissionApi
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
        $permission = has_permission($request->route()->getName(), null, false, true);
        if ($permission === true) {
            return $next($request);
        }
        return response()->json([401 =>api_permission($permission)]);
    }
}
