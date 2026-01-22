<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('is_admin')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
