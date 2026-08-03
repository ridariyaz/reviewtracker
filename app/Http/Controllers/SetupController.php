<?php

namespace App\Http\Controllers;

use App\Services\CompanyContext;
use App\Services\LogoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * First-run company setup: name, branding, and required Google review URL.
 */
class SetupController extends Controller
{
    public function show(CompanyContext $companies)
    {
        $user = Auth::user();
        $company = $companies->ensureDefaultCompany($user);

        if ($company->hasValidGoogleReviewUrl()) {
            return redirect()->route('admin');
        }

        $industries = [
            'General Retail',
            'Restaurant & Dining',
            'Electronics & Repair',
            'Automotive & Repair',
            'Beauty & Salon',
            'Medical & Dental',
            'Fitness & Wellness',
            'Professional Services',
            'Home & Trades Services',
        ];

        return view('setup.index', [
            'company' => $company,
            'brandName' => $company->name,
            'brandLogoUrl' => $company->logo_url,
            'companies' => $companies->companiesFor($user),
            'currentCompany' => $company,
            'setupRequired' => true,
            'industries' => $industries,
        ]);
    }

    public function store(Request $request, CompanyContext $companies, LogoService $logos)
    {
        $user = Auth::user();
        $company = $companies->ensureDefaultCompany($user);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'logo_file' => ['nullable', 'image', 'max:4096'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'google_review_url' => ['required', 'url', 'max:2048'],
        ]);

        if (! $this->isAcceptableReviewUrl($data['google_review_url'])) {
            throw ValidationException::withMessages([
                'google_review_url' => 'Enter a full Google review / Maps link (not just google.com). See Help for how to find it.',
            ]);
        }

        $logoResult = $logos->saveAndExtractColors(
            $request->file('logo_file'),
            $company->id
        );

        $company->update([
            'name' => $data['name'],
            'industry' => $data['industry'] ?? null,
            'logo_url' => $logoResult['logo_url'] ?: ($data['logo_url'] ?: $company->logo_url),
            'primary_color' => $data['primary_color'] ?? ($logoResult['primary_hex'] ?? '#0d6efd'),
            'secondary_color' => $data['secondary_color'] ?? ($logoResult['secondary_hex'] ?? '#111827'),
            'google_review_url' => $data['google_review_url'],
        ]);

        return redirect()
            ->route('admin')
            ->with('success', 'Company setup complete. You can now add employees and generate QR codes.')
            ->with('trigger_tour', true);
    }

    private function isAcceptableReviewUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (! $parts || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '/';

        // Reject bare Google homepage — customers need a place/review destination.
        if (in_array($host, ['google.com', 'www.google.com'], true) && ($path === '/' || $path === '')) {
            return false;
        }

        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
