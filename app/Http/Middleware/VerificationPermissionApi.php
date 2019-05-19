<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerificationPermissionApi
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
        $auth = Auth::user();
        if(
            (!$auth || ($auth && $auth->is_email_verified == EMAIL_VERIFICATION_STATUS_INACTIVE) ) &&
            admin_settings('require_email_verification') == ACTIVE_STATUS_ACTIVE
        ){
            return $next($request);
        }
        return response()->json([401 =>api_permission(ROUTE_REDIRECT_TO_UNAUTHORIZED)]);
    }
}
