<?php

namespace App\Http\Controllers;

use App\Services\CompanyContext;
use App\Services\LogoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    public function index(CompanyContext $companies)
    {
        $user = Auth::user();
        $company = $companies->currentFor($user);

        return view('settings.index', [
            'user' => $user,
            'company' => $company,
            'companies' => $companies->companiesFor($user),
            'currentCompany' => $company,
            'brandName' => $company?->name ?? config('app.name'),
            'languages' => [
                'en' => ['name' => 'English', 'dir' => 'ltr', 'flag' => '🇬🇧'],
                'ml' => ['name' => 'Malayalam (മലയാളം)', 'dir' => 'ltr', 'flag' => '🇮🇳'],
                'ar' => ['name' => 'Arabic (العربية)', 'dir' => 'rtl', 'flag' => '🇸🇦'],
                'hi' => ['name' => 'Hindi (हिंदी)', 'dir' => 'ltr', 'flag' => '🇮🇳'],
                'bn' => ['name' => 'Bengali / Bangladeshi (বাংলা)', 'dir' => 'ltr', 'flag' => '🇧🇩'],
            ],
            'industries' => [
                'Electronics & Repair',
                'Retail & E-commerce',
                'Restaurant & Cafe',
                'Medical & Dental Clinic',
                'Electronics & Computers',
                'Automotive & Repair Shop',
                'Salon, Spa & Beauty',
                'Real Estate & Property',
                'Professional Services',
                'Other',
            ],
        ]);
    }

    public function updateCompany(Request $request, CompanyContext $companies, LogoService $logos)
    {
        $user = Auth::user();
        $company = $companies->currentFor($user);

        if (! $company) {
            return redirect()->back()->withErrors(['company' => 'No active company found.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo_file' => ['nullable', 'image', 'max:4096'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'google_review_url' => ['nullable', 'url', 'max:2048'],
            'industry' => ['nullable', 'string', 'max:255'],
            'keywords' => ['nullable', 'string', 'max:1000'],
            'language' => ['required', 'in:en,ml,ar,hi,bn'],
            'default_platform' => ['nullable', 'string', 'max:100'],
            'custom_link_name' => ['nullable', 'array'],
            'custom_link_name.*' => ['nullable', 'string', 'max:255'],
            'custom_link_url' => ['nullable', 'array'],
            'custom_link_url.*' => ['nullable', 'url', 'max:2048'],
            'enable_multi_review_prompt' => ['nullable', 'boolean'],
            'enable_gamification' => ['nullable', 'boolean'],
            'gamification_mode' => ['nullable', 'in:random,employee'],
            'gamification_interval' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'gamification_reward' => ['nullable', 'string', 'max:255'],
            'gamification_image_file' => ['nullable', 'image', 'max:4096'],
        ]);

        $logoResult = $logos->saveAndExtractColors(
            $request->file('logo_file'),
            $company->id
        );

        $gamificationImageUrl = $company->gamification_image_url;
        if ($request->hasFile('gamification_image_file')) {
            $path = $request->file('gamification_image_file')->store('gamification', 'public');
            $gamificationImageUrl = '/storage/'.$path;
        }

        $customLinks = [];
        if (isset($data['custom_link_name']) && is_array($data['custom_link_name'])) {
            foreach ($data['custom_link_name'] as $index => $linkName) {
                $url = $data['custom_link_url'][$index] ?? null;
                if (! empty($linkName) && ! empty($url)) {
                    $customLinks[] = [
                        'name' => trim($linkName),
                        'url' => trim($url),
                    ];
                }
            }
        }

        $company->update([
            'name' => $data['name'],
            'logo_url' => $logoResult['logo_url'] ?: $company->logo_url,
            'primary_color' => ($data['primary_color'] ?? null) ?: ($logoResult['primary_hex'] ?? $company->primary_color),
            'secondary_color' => ($data['secondary_color'] ?? null) ?: ($logoResult['secondary_hex'] ?? $company->secondary_color),
            'google_review_url' => $data['google_review_url'] ?? null,
            'industry' => $data['industry'] ?? null,
            'keywords' => $data['keywords'] ?? null,
            'language' => $data['language'],
            'default_platform' => $data['default_platform'] ?? 'google',
            'custom_links' => $customLinks,
            'enable_multi_review_prompt' => $request->has('enable_multi_review_prompt'),
            'enable_gamification' => $request->has('enable_gamification'),
            'gamification_mode' => $data['gamification_mode'] ?? 'random',
            'gamification_interval' => $data['gamification_interval'] ?? 50,
            'gamification_reward' => $data['gamification_reward'] ?? 'Free Coffee / Gift Voucher',
            'gamification_image_url' => $gamificationImageUrl,
        ]);

        return redirect()->back()->with('success_pref', 'Settings updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password does not match our records.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return redirect()->back()->with('success_password', 'Password updated successfully.');
    }
}
