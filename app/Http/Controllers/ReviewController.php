<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Feedback;
use App\Models\ScanLog;
use Illuminate\Http\Request;

/**
 * Public customer review funnel (no authentication).
 *
 * Flow: /review/{id} → Good redirects to Google review URL;
 * OK/Bad collect private comments then /thankyou.
 */
class ReviewController extends Controller
{
    /** Customer landing page after scanning an employee QR. */
    public function show(Request $request, Employee $employee, \App\Services\LanguageService $langService)
    {
        $company = $employee->company;

        ScanLog::create([
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'device_type' => $this->detectDeviceType($request->userAgent()),
        ]);

        $lang = $request->query('lang', $company?->language ?? 'en');
        $translations = $langService->getTranslations($lang);

        $totalScans = ScanLog::where('company_id', $employee->company_id)->count();
        $enableGamification = (bool) ($company?->enable_gamification);
        $interval = max(1, (int) ($company?->gamification_interval ?? 50));
        $isWinner = $enableGamification && ($totalScans > 0) && ($totalScans % $interval === 0);
        $winnerCode = 'WIN-' . strtoupper(substr(md5($employee->id . '-' . $totalScans), 0, 6));

        return view('review.feedback', [
            'employeeId' => $employee->id,
            'brandName' => $company?->name ?? config('app.name'),
            'brandLogoUrl' => $company?->logo_url,
            'brandPrimaryColor' => $company?->primary_color ?? '#0d6efd',
            'brandSecondaryColor' => $company?->secondary_color ?? '#020617',
            'txt' => $translations,
            'enableGamification' => $enableGamification,
            'gamificationInterval' => $interval,
            'gamificationReward' => $company?->gamification_reward ?? 'Free Coffee / Voucher',
            'isWinner' => $isWinner,
            'winnerCode' => $winnerCode,
        ]);
    }

    /**
     * Record a "good" rating and send the customer to the company's Google review page.
     * Falls back to google.com when google_review_url is not configured.
     */
    public function good(Employee $employee, \App\Services\LanguageService $langService)
    {
        $company = $employee->company;

        if (! $company || ! $company->hasValidGoogleReviewUrl()) {
            return view('review.no_link', [
                'brandName' => $company?->name ?? config('app.name'),
                'brandPrimaryColor' => $company?->primary_color ?? '#0d6efd',
                'brandSecondaryColor' => $company?->secondary_color ?? '#020617',
            ]);
        }

        $googleUrl = $company->google_review_url;

        $employee->increment('scans');
        $employee->increment('good_count');

        Feedback::create([
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'rating' => 'good',
            'comment' => '',
            'status' => 'new',
        ]);

        $totalScans = ScanLog::where('company_id', $employee->company_id)->count();
        $enableGamification = (bool) ($company?->enable_gamification);
        $interval = max(1, (int) ($company?->gamification_interval ?? 50));
        $isWinner = $enableGamification && ($totalScans > 0) && ($totalScans % $interval === 0);
        $winnerCode = 'WIN-' . strtoupper(substr(md5($employee->id . '-' . $totalScans), 0, 6));

        return view('review.good', [
            'employee' => $employee,
            'brandName' => $company->name,
            'brandLogoUrl' => $company->logo_url,
            'brandPrimaryColor' => $company->primary_color ?? '#0d6efd',
            'brandSecondaryColor' => $company->secondary_color ?? '#020617',
            'googleReviewUrl' => $googleUrl,
            'industry' => $company->industry ?? '',
            'keywords' => $company->keywords ?? '',
            'enableGamification' => $enableGamification,
            'gamificationReward' => $company?->gamification_reward ?? 'Free Coffee / Voucher',
            'isWinner' => $isWinner,
            'winnerCode' => $winnerCode,
        ]);
    }

    /** Show private feedback form for an "OK" rating. */
    public function ok(Employee $employee)
    {
        return $this->internalForm($employee, 'ok');
    }

    /** Show private feedback form for a "Bad" rating. */
    public function bad(Employee $employee)
    {
        return $this->internalForm($employee, 'bad');
    }

    /** Persist OK/Bad private comments and bump employee counters. */
    public function submitInternal(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'rating' => ['required', 'in:ok,bad'],
            'comment' => ['nullable', 'string'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);

        Feedback::create([
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? '',
            'status' => 'new',
        ]);

        $employee->increment('scans');
        if ($data['rating'] === 'ok') {
            $employee->increment('ok_count');
        } else {
            $employee->increment('bad_count');
        }

        return redirect()->route('thankyou');
    }

    public function thankyou(\App\Services\LanguageService $langService)
    {
        return view('review.thankyou', [
            'brandName' => config('app.name'),
            'txt' => $langService->getTranslations('en'),
        ]);
    }

    private function internalForm(Employee $employee, string $rating)
    {
        $company = $employee->company;
        $langService = new \App\Services\LanguageService();
        $translations = $langService->getTranslations($company?->language);

        return view('review.internal', [
            'employeeId' => $employee->id,
            'rating' => $rating,
            'brandName' => $company?->name ?? config('app.name'),
            'brandLogoUrl' => $company?->logo_url,
            'brandPrimaryColor' => $company?->primary_color ?? '#0d6efd',
            'brandSecondaryColor' => $company?->secondary_color ?? '#020617',
            'txt' => $translations,
        ]);
    }

    private function detectDeviceType(?string $ua): string
    {
        if (! $ua) {
            return 'mobile';
        }
        $ua = strtolower($ua);
        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet')) {
            return 'tablet';
        }
        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone') || str_contains($ua, 'android')) {
            return 'mobile';
        }

        return 'desktop';
    }
}
