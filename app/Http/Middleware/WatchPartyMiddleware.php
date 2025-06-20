<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WatchPartyMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        if (!gs('watch_party')) {
            $notify[] = ['error', 'Watch party is now disabled in this system'];
            return back()->withNotify($notify);
        }
        return $next($request);
    }
}
