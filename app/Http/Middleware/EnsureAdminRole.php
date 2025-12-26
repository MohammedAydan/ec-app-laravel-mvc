<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EnsureAdminRole
{
    /**
     * Allow only users with role name admin or owner.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $roleName = $user?->role?->name;

        if (!in_array($roleName, ['admin', 'owner'], true)) {
            throw new HttpException(403, 'Forbidden');
        }

        return $next($request);
    }
}
