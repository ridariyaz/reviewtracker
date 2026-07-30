<?php

namespace App\Http\Controllers;

use App\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;

/**
 * Admin dashboard: employee list, QR previews, and leaderboard for the active company.
 */
class AdminController extends Controller
{
    public function index(CompanyContext $companies)
    {
        $user = Auth::user();
        $company = $companies->ensureDefaultCompany($user);
        $companyList = $companies->companiesFor($user);
        $employees = $company->employees()->orderByDesc('scans')->orderBy('name')->get();

        return view('admin.index', [
            'employees' => $employees,
            'companies' => $companyList,
            'currentCompany' => $company,
            'brandName' => $company->name,
            'brandLogoUrl' => $company->logo_url,
        ]);
    }
}
