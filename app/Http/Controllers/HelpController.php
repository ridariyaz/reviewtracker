<?php

namespace App\Http\Controllers;

use App\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;

/** Static help content for admins (features + how to get a Google review URL). */
class HelpController extends Controller
{
    public function show(CompanyContext $companies)
    {
        $user = Auth::user();
        $company = $companies->ensureDefaultCompany($user);

        return view('help.index', [
            'brandName' => $company->name,
            'brandLogoUrl' => $company->logo_url,
            'companies' => $companies->companiesFor($user),
            'currentCompany' => $company,
            'setupComplete' => $company->hasValidGoogleReviewUrl(),
        ]);
    }
}
