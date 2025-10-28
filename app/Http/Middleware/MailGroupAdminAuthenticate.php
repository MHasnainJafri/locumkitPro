<?php

namespace App\Http\Middleware;

use App\Models\MailGroupUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MailGroupAdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('mail_group_web')->check()) {
            return redirect()->route('email-grouping.login');
        }
        if (auth()->guard('mail_group_web')->user()->role != MailGroupUser::USER_ROLE_ADMIN) {
            return abort(403);
        }
        return $next($request);
    }
}
