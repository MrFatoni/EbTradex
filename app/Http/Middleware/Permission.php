<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\UnauthorizedException;

class Permission
{
    /**
     * @developer: M.G. Rabbi
     * @date: 2018-08-08 7:21 PM
     * @description:
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        $permission = has_permission($request->route()->getName(), null, false);
        if ($permission === true) {
            return $next($request);
        }
        throw new UnauthorizedException($permission,401);
    }
}
