<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Employee self-service portal (employee guard).
 * Shows personal QR, stats, feedback, and company leaderboard.
 */
class EmployeePortalController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('employee')->check()) {
            return redirect()->route('employee.dashboard');
        }

        return view('employee.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Avoid mixing admin + employee sessions in the same browser.
        Auth::guard('web')->logout();

        $ok = Auth::guard('employee')->attempt([
            'employee_username' => $credentials['username'],
            'password' => $credentials['password'],
        ]);

        if (! $ok) {
            return back()->withInput()->with('error', 'Invalid employee credentials. Please try again.');
        }

        $request->session()->regenerate();

        return redirect()->route('employee.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('employee.login');
    }

    public function dashboard()
    {
        /** @var Employee $employee */
        $employee = Auth::guard('employee')->user();
        $company = $employee->company;

        $leaderboard = Employee::query()
            ->where('company_id', $employee->company_id)
            ->orderByDesc('scans')
            ->orderBy('name')
            ->get();

        $feedbackRows = Feedback::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->get();

        return view('employee.dashboard', [
            'employee' => $employee,
            'leaderboard' => $leaderboard,
            'feedbackRows' => $feedbackRows,
            'brandName' => $company?->name ?? config('app.name'),
            'brandLogoUrl' => $company?->logo_url,
            'brandPrimaryColor' => $company?->primary_color ?? '#0d6efd',
            'brandSecondaryColor' => $company?->secondary_color ?? '#0f172a',
        ]);
    }

    public function qr()
    {
        $employee = Auth::guard('employee')->user();
        $company = $employee->company;

        return view('employee.qr', [
            'employee' => $employee,
            'employeeId' => $employee->id,
            'brandName' => $company?->name ?? config('app.name'),
            'brandLogoUrl' => $company?->logo_url,
            'brandPrimaryColor' => $company?->primary_color ?? '#0d6efd',
            'brandSecondaryColor' => $company?->secondary_color ?? '#020617',
            'enableGamification' => (bool) ($company?->enable_gamification),
            'gamificationInterval' => $company?->gamification_interval ?? 50,
            'gamificationReward' => $company?->gamification_reward ?? 'Free Coffee / Gift Voucher',
        ]);
    }

    public function toggleForceWin(Request $request)
    {
        /** @var Employee $employee */
        $employee = Auth::guard('employee')->user();
        $employee->update([
            'force_next_win' => true,
        ]);

        return redirect()->back()->with('success', '🎁 Next customer scan is GUARANTEED to win the reward prize!');
    }
}
