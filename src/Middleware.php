<?php
/**
 * This class implements a Laravel Middleware for SPIDAuth Package.
 *
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth;

use Closure;

class Middleware
{
    /**
     * Check if the current session is authenticated with SPID, redirect if not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!app('SPIDAuth')->isAuthenticated()) {
            return redirect()->guest(route('spid-auth_login'));
        }
        return $next($request);
    }
}
