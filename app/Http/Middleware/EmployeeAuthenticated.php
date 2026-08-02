<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeAuthenticated
{
    // `handle()` runs before the controller. If there's no employee_id in
    // the session, we redirect to the employee login page and the actual
    // controller code never runs -- same effect as your Python decorator
    // that wrapped view functions with a login check.
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('employee_id')) {
            return redirect()->route('employee.login');
        }

        return $next($request);
    }
}
