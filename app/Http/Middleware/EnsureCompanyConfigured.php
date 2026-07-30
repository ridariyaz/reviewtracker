<?php

namespace App\Http\Middleware;

use App\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block admin feature pages until the active company has a valid Google review URL.
 */
class EnsureCompanyConfigured
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $company = app(CompanyContext::class)->ensureDefaultCompany($user);

        if ($company->hasValidGoogleReviewUrl()) {
            return $next($request);
        }

        return redirect()
            ->route('setup.show')
            ->with('error', 'Finish company setup and add a valid Google review URL before using the rest of ReviewTracker.');
    }
}
