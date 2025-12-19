<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class InvigilatorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth()->check() || Auth()->user()->role !== 'supervisor') {
            abort(403, 'غير مصرح لك بالدخول');
        }

        return $next($request);
    }
}
