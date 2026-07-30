<?php

namespace App\Http\Controllers;

use App\Services\CompanyContext;
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
            'brandName' => $company?->name ?? config('app.name'),
            'languages' => [
                'en' => ['name' => 'English', 'dir' => 'ltr', 'flag' => '🇺🇸'],
                'ml' => ['name' => 'Malayalam (മലയാളം)', 'dir' => 'ltr', 'flag' => '🇮🇳'],
                'hi' => ['name' => 'Hindi (हिंदी)', 'dir' => 'ltr', 'flag' => '🇮🇳'],
                'ar' => ['name' => 'Arabic (العربية)', 'dir' => 'rtl', 'flag' => '🇦🇪'],
            ],
        ]);
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

    public function updatePreferences(Request $request, CompanyContext $companies)
    {
        $user = Auth::user();
        $company = $companies->currentFor($user);

        if (! $company) {
            return redirect()->back()->withErrors(['company' => 'No active company found.']);
        }

        $data = $request->validate([
            'language' => ['required', 'in:en,ml,hi,ar'],
            'custom_link_name' => ['nullable', 'array'],
            'custom_link_name.*' => ['nullable', 'string', 'max:255'],
            'custom_link_url' => ['nullable', 'array'],
            'custom_link_url.*' => ['nullable', 'url', 'max:2048'],
        ]);

        $customLinks = [];
        if (isset($data['custom_link_name']) && is_array($data['custom_link_name'])) {
            foreach ($data['custom_link_name'] as $index => $name) {
                $url = $data['custom_link_url'][$index] ?? null;
                if (! empty($name) && ! empty($url)) {
                    $customLinks[] = [
                        'name' => trim($name),
                        'url' => trim($url),
                    ];
                }
            }
        }

        $company->update([
            'language' => $data['language'],
            'custom_links' => $customLinks,
        ]);

        return redirect()->back()->with('success_pref', 'Settings saved successfully.');
    }
}
