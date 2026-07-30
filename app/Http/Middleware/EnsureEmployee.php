<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Require an authenticated employee (employee guard). Alias: employee */
class EnsureEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('employee')->check()) {
            return redirect()->route('employee.login');
        }

        return $next($request);
    }
}
