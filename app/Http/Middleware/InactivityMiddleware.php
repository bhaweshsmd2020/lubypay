<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InactivityMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $timeout = 5 * 60;
            $lastActivity = Session::get('last_activity_time');
            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                Auth::logout();
                Session::flush();
                return redirect()->route('login')->with('message', 'You have been logged out due to inactivity.');
            }
            Session::put('last_activity_time', time());
        }
        return $next($request);
    }
}
