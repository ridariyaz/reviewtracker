<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\CompanyContext;
use App\Services\LogoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Multi-company management: create/update brand kit and switch the active company in session.
 *
 * google_review_url on the active company is where "Good" feedback redirects customers.
 * A valid review URL is required when creating or updating a company.
 */
class CompanyController extends Controller
{
    public function index(CompanyContext $companies)
    {
        $user = Auth::user();
        $companyList = $companies->companiesFor($user);
        $current = $companies->currentFor($user);

        return view('companies.index', [
            'companies' => $companyList,
            'currentCompany' => $current,
            'brandName' => $current?->name ?? config('app.name'),
            'brandLogoUrl' => $current?->logo_url,
        ]);
    }

    public function store(Request $request, LogoService $logos)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo_file' => ['nullable', 'image', 'max:4096'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'google_review_url' => ['required', 'url', 'max:2048'],
            'tripadvisor_review_url' => ['nullable', 'url', 'max:2048'],
            'yelp_review_url' => ['nullable', 'url', 'max:2048'],
            'trustpilot_review_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $this->assertAcceptableReviewUrl($data['google_review_url']);

        $company = Auth::user()->companies()->create([
            'name' => $data['name'],
        ]);

        [$logoUrl, $autoPrimary, $autoSecondary] = $logos->saveAndExtractColors(
            $request->file('logo_file'),
            $company->id
        );

        $company->update([
            'logo_url' => $logoUrl ?: ($data['logo_url'] ?? null),
            'primary_color' => $autoPrimary ?: ($data['primary_color'] ?? '#0d6efd'),
            'secondary_color' => $autoSecondary ?: ($data['secondary_color'] ?? '#111827'),
            'google_review_url' => $data['google_review_url'],
            'tripadvisor_review_url' => $data['tripadvisor_review_url'] ?? null,
            'yelp_review_url' => $data['yelp_review_url'] ?? null,
            'trustpilot_review_url' => $data['trustpilot_review_url'] ?? null,
        ]);

        session(['company_id' => $company->id]);

        return redirect()->route('companies.index');
    }

    public function update(Request $request, Company $company, LogoService $logos)
    {
        abort_unless($company->user_id === Auth::id(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo_file' => ['nullable', 'image', 'max:4096'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'google_review_url' => ['required', 'url', 'max:2048'],
            'tripadvisor_review_url' => ['nullable', 'url', 'max:2048'],
            'yelp_review_url' => ['nullable', 'url', 'max:2048'],
            'trustpilot_review_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $this->assertAcceptableReviewUrl($data['google_review_url']);

        [$logoUrl, $autoPrimary, $autoSecondary] = $logos->saveAndExtractColors(
            $request->file('logo_file'),
            $company->id
        );

        $company->update([
            'name' => $data['name'],
            'logo_url' => $logoUrl ?: ($data['logo_url'] ?: $company->logo_url),
            'primary_color' => $autoPrimary ?: ($data['primary_color'] ?? $company->primary_color),
            'secondary_color' => $autoSecondary ?: ($data['secondary_color'] ?? $company->secondary_color),
            'google_review_url' => $data['google_review_url'],
            'tripadvisor_review_url' => $data['tripadvisor_review_url'] ?? null,
            'yelp_review_url' => $data['yelp_review_url'] ?? null,
            'trustpilot_review_url' => $data['trustpilot_review_url'] ?? null,
        ]);

        return redirect()->route('companies.index');
    }

    /** Persist the selected company id in session for subsequent admin pages. */
    public function switch(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required', 'integer'],
        ]);

        $company = Auth::user()->companies()->whereKey($data['company_id'])->first();
        if ($company) {
            session(['company_id' => $company->id]);
        }

        return redirect()->back();
    }

    private function assertAcceptableReviewUrl(string $url): void
    {
        $temp = new Company(['google_review_url' => $url]);
        if (! $temp->hasValidGoogleReviewUrl()) {
            throw ValidationException::withMessages([
                'google_review_url' => 'Enter a full Google review / Maps link (not just google.com). See Help for how to find it.',
            ]);
        }
    }
}
