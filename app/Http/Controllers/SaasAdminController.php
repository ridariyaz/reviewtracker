<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Feedback;
use App\Models\SaasSetting;
use App\Models\ScanLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * SaaS Super Admin Controller for platform management, account troubleshooting,
 * custom script code injections, and system health diagnostics.
 */
class SaasAdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $totalEmployees = Employee::count();
        $totalScans = ScanLog::count();
        $totalFeedback = Feedback::count();

        $recentUsers = User::withCount('companies')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        return view('saas_admin.index', [
            'totalUsers' => $totalUsers,
            'totalCompanies' => $totalCompanies,
            'totalEmployees' => $totalEmployees,
            'totalScans' => $totalScans,
            'totalFeedback' => $totalFeedback,
            'recentUsers' => $recentUsers,
            'globalAnnouncement' => SaasSetting::get('global_announcement', ''),
            'globalScript' => SaasSetting::get('global_script', ''),
        ]);
    }

    public function users(Request $request)
    {
        $query = User::withCount(['companies']);

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(20);

        return view('saas_admin.users', [
            'users' => $users,
        ]);
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot suspend your own Super Admin account.');
        }

        $newStatus = $user->status === 'suspended' ? 'active' : 'suspended';
        $user->update(['status' => $newStatus]);

        return redirect()->back()->with('success', "Account {$user->username} status updated to {$newStatus}.");
    }

    public function toggleSuperAdmin(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot alter your own Super Admin status.');
        }

        $user->update(['is_superadmin' => ! $user->is_superadmin]);

        return redirect()->back()->with('success', "Super admin privilege updated for {$user->username}.");
    }

    public function impersonate(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('saas_admin.index');
        }

        session(['impersonator_id' => Auth::id()]);
        Auth::guard('web')->login($user);

        return redirect()->route('admin')->with('info', "Troubleshooting Mode: Logged in as {$user->username}. Click 'Exit Troubleshoot' in top bar to return to SaaS Admin.");
    }

    public function stopImpersonating()
    {
        $impersonatorId = session('impersonator_id');
        if ($impersonatorId) {
            $admin = User::find($impersonatorId);
            if ($admin && $admin->isSuperAdmin()) {
                session()->forget('impersonator_id');
                Auth::guard('web')->login($admin);
                return redirect()->route('saas_admin.index')->with('success', 'Exited troubleshooting mode and returned to SaaS Admin.');
            }
        }

        return redirect()->route('admin');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Clean up user's companies & related records
        foreach ($user->companies as $company) {
            Employee::where('company_id', $company->id)->delete();
            Feedback::where('company_id', $company->id)->delete();
            ScanLog::where('company_id', $company->id)->delete();
            $company->delete();
        }
        $user->delete();

        return redirect()->back()->with('success', "User account {$user->username} and associated data deleted.");
    }

    public function codeInjector()
    {
        $companies = Company::with('user')->orderBy('name')->get();

        return view('saas_admin.code_injector', [
            'companies' => $companies,
            'globalScript' => SaasSetting::get('global_script', ''),
            'globalAnnouncement' => SaasSetting::get('global_announcement', ''),
        ]);
    }

    public function saveCode(Request $request)
    {
        $data = $request->validate([
            'global_script' => ['nullable', 'string'],
            'global_announcement' => ['nullable', 'string'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'company_custom_code' => ['nullable', 'string'],
        ]);

        SaasSetting::set('global_script', $data['global_script'] ?? '');
        SaasSetting::set('global_announcement', $data['global_announcement'] ?? '');

        if (! empty($data['company_id'])) {
            $company = Company::find($data['company_id']);
            if ($company) {
                $company->update(['custom_code' => $data['company_custom_code'] ?? null]);
            }
        }

        return redirect()->back()->with('success', 'Custom code injections and global announcement updated successfully.');
    }

    public function diagnostics()
    {
        $dbDriver = config('database.default');
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();

        $tableStats = [
            'users' => User::count(),
            'companies' => Company::count(),
            'employees' => Employee::count(),
            'feedback' => Feedback::count(),
            'scan_logs' => ScanLog::count(),
        ];

        return view('saas_admin.diagnostics', [
            'dbDriver' => $dbDriver,
            'phpVersion' => $phpVersion,
            'laravelVersion' => $laravelVersion,
            'tableStats' => $tableStats,
        ]);
    }
}
