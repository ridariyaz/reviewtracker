<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Admin signup / login / logout (web guard → users table).
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin');
        }

        return view('auth.login');
    }

    public function login(Request $request, CompanyContext $companies)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            return back()->withInput()->with('error', 'Invalid credentials. Please try again.');
        }

        $request->session()->regenerate();
        $company = $companies->ensureDefaultCompany(Auth::user());

        if (! $company->hasValidGoogleReviewUrl()) {
            return redirect()->route('setup.show');
        }

        return redirect()->route('admin');
    }

    public function showSignup()
    {
        return view('auth.signup');
    }

    public function signup(Request $request, CompanyContext $companies)
    {
        $data = $request->validate([
            'email' => ['nullable', 'email'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:4'],
        ]);

        $user = User::create([
            'email' => $data['email'] ?? null,
            'username' => $data['username'],
            'password' => $data['password'],
            'is_admin' => true,
            'provider' => 'local',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $companies->ensureDefaultCompany($user);

        return redirect()
            ->route('setup.show')
            ->with('success', 'Account created. Set up your company and Google review URL to continue.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
