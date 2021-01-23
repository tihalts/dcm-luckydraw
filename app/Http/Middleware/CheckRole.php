<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next , $role = 'admin')
    {
        if (! $request->expectsJson() && $request->user()->role != $role) {
            return  redirect()->route('login');
        } else if ($request->user()->role != $role ){
            abort(401, 'This action is unauthorized.');
        }
        return $next($request);
    }
}
