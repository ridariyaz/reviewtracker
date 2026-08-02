<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeDashboardController extends Controller
{
    // GET /employee/dashboard
    public function index(Request $request): View
    {
        $employee = Employee::findOrFail(session('employee_id'));
        $company = $employee->company;

        $leaderboard = $company->employees()
            ->orderByDesc('scans')
            ->orderBy('name')
            ->get();

        $feedbackRows = $employee->feedback()
            ->orderByDesc('created_at')
            ->get();

        return view('employee.dashboard', [
            'employee' => $employee,
            'company' => $company,
            'leaderboard' => $leaderboard,
            'feedbackRows' => $feedbackRows,
        ]);
    }

    // GET /employee/qr -- fullscreen QR code, meant to be displayed at a
    // desk/counter for customers to scan
    public function qrFullscreen(): View
    {
        $employee = Employee::findOrFail(session('employee_id'));

        return view('employee.qr', compact('employee'));
    }
}
