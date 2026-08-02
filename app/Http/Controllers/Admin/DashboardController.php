<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // GET /admin -- the main employee stats table
    public function index(Request $request): View
    {
        $user = $request->user();
        $company = $user->currentCompany();
        $companies = $user->companies()->orderBy('id')->get();

        $employees = $company
            ? $company->employees()->orderByDesc('scans')->get()
            : collect();

        return view('admin.dashboard', compact('employees', 'company', 'companies'));
    }

    // GET /admin/feedback -- the internal feedback inbox
    public function feedback(Request $request): View
    {
        $company = $request->user()->currentCompany();

        $feedback = $company
            ? Feedback::with('employee')
                ->where('company_id', $company->id)
                ->orderByDesc('created_at')
                ->get()
            : collect();

        return view('admin.feedback', compact('feedback', 'company'));
    }

    // POST /admin/feedback/{feedback}/status
    public function updateStatus(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'status' => 'required|in:new,in_progress,resolved',
        ]);

        $feedback->update(['status' => $data['status']]);

        return redirect()->route('admin.feedback');
    }

    // GET /admin/export/employees.csv
    public function exportEmployeesCsv(Request $request): Response
    {
        $company = $request->user()->currentCompany();
        $employees = $company
            ? $company->employees()->orderByDesc('scans')->orderBy('name')->get()
            : collect();

        $lines = ['id,name,scans,good_count,ok_count,bad_count'];
        foreach ($employees as $e) {
            $name = str_replace('"', '""', $e->name);
            $lines[] = "{$e->id},\"{$name}\",{$e->scans},{$e->good_count},{$e->ok_count},{$e->bad_count}";
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employees.csv"',
        ]);
    }

    // GET /admin/export/feedback.csv
    public function exportFeedbackCsv(Request $request): Response
    {
        $company = $request->user()->currentCompany();
        $rows = $company
            ? Feedback::with('employee')
                ->where('company_id', $company->id)
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $lines = ['id,employee_id,employee_name,rating,comment,status,created_at'];
        foreach ($rows as $f) {
            $name = str_replace('"', '""', $f->employee->name ?? '');
            $comment = str_replace('"', '""', $f->comment ?? '');
            $lines[] = "{$f->id},{$f->employee_id},\"{$name}\",{$f->rating},\"{$comment}\",{$f->status},{$f->created_at}";
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="feedback.csv"',
        ]);
    }
}
