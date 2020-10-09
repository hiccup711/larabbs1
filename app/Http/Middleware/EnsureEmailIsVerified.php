<?php

namespace App\Http\Middleware;

use Closure;

class EnsureEmailIsVerified
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
        if($request->user() &&
           ! $request->user()->hasVerifiedEmail() &&
           ! $request->is('email/*', 'logout'))
        {
            return $request->expectsJson() ? abort(403, "您的邮箱还未验证") : redirect()->route('verification.notice');
        }
        return $next($request);
    }
}
