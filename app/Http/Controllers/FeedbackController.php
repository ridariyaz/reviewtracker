<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Services\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin feedback inbox, status updates, and CSV exports for the active company.
 */
class FeedbackController extends Controller
{
    public function index(CompanyContext $companies)
    {
        $company = $companies->ensureDefaultCompany(Auth::user());

        $feedback = Feedback::query()
            ->with('employee')
            ->where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('feedback.index', [
            'feedback' => $feedback,
            'brandName' => $company->name,
            'brandLogoUrl' => $company->logo_url,
            'companies' => $companies->companiesFor(Auth::user()),
            'currentCompany' => $company,
        ]);
    }

    public function updateStatus(Request $request, Feedback $feedback, CompanyContext $companies)
    {
        $company = $companies->ensureDefaultCompany(Auth::user());
        abort_unless($feedback->company_id === $company->id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:new,in_progress,resolved'],
        ]);

        $feedback->update(['status' => $data['status']]);

        return redirect()->route('feedback.index');
    }

    public function exportEmployees(CompanyContext $companies): StreamedResponse
    {
        $company = $companies->ensureDefaultCompany(Auth::user());
        $rows = $company->employees()->orderByDesc('scans')->orderBy('name')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'name', 'scans', 'good_count', 'ok_count', 'bad_count']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->id, $row->name, $row->scans, $row->good_count, $row->ok_count, $row->bad_count]);
            }
            fclose($out);
        }, 'employees.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportFeedback(CompanyContext $companies): StreamedResponse
    {
        $company = $companies->ensureDefaultCompany(Auth::user());
        $rows = Feedback::query()
            ->with('employee')
            ->where('company_id', $company->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'employee_id', 'employee_name', 'rating', 'comment', 'status', 'created_at']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->id,
                    $row->employee_id,
                    $row->employee?->name,
                    $row->rating,
                    $row->comment,
                    $row->status,
                    $row->created_at,
                ]);
            }
            fclose($out);
        }, 'feedback.csv', ['Content-Type' => 'text/csv']);
    }
}
