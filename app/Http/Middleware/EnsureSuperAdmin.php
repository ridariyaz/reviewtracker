<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Require an authenticated Super Admin user. Alias: superadmin */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized access to SaaS Super Admin portal.');
        }

        return $next($request);
    }
}
