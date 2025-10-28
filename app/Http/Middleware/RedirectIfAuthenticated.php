<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard == "mail_group_web") {
                    return redirect(RouteServiceProvider::MAILGROUP_HOME);
                } else {
                    if ($request->session()->has('url.intended')) {
                        // Get the intended URL and remove it from the session
                        $intendedUrl = $request->session()->pull('url.intended');
                        // Redirect the user to the intended URL
                        return redirect($intendedUrl);
                    }
                    if (Gate::check("is_freelancer")) {
                        return redirect(RouteServiceProvider::FREELANCER_DASHBOARD);
                    }
                    if (Gate::check("is_employer")) {
                        return redirect(RouteServiceProvider::EMPLOYER_DASHBOARD);
                    }
                    return redirect(RouteServiceProvider::HOME);
                }
            }
        }

        return $next($request);
    }
}
