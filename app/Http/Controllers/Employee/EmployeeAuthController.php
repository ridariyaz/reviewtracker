<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

// Employees log in separately from admins (different credentials, different
// session key). We store their identity in session('employee_id') rather
// than using Laravel's built-in auth() system, mirroring how the Python
// version used a distinct session["employee_id"] instead of the admin's
// session["admin_logged_in"].
class EmployeeAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('employee.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $employee = Employee::where('employee_username', $data['username'])->first();

        if ($employee && $employee->employee_password && Hash::check($data['password'], $employee->employee_password)) {
            $request->session()->regenerate(); // rotates the session ID on login, a security best practice against session fixation attacks
            session([
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
            ]);

            return redirect()->route('employee.dashboard');
        }

        return back()->withErrors(['username' => 'Invalid employee credentials. Please try again.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['employee_id', 'employee_name']);

        return redirect()->route('employee.login');
    }
}
