<?php

namespace WebsolutionsGroup\Auth\Middleware;

use Closure;
use WebsolutionsGroup\Auth\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::guest()) {
            return redirect('/home');
        }

        return $next($request);
    }
}
