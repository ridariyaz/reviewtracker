<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\ScanLog;
use App\Services\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Admin analytics: QR scan conversion funnel, daily trends, and per-employee performance.
 */
class AnalyticsController extends Controller
{
    public function index(Request $request, CompanyContext $companies)
    {
        $company = $companies->ensureDefaultCompany(Auth::user());
        $rangeKey = $request->query('range', '30d');
        $rangeMap = ['7d' => 7, '30d' => 30, '6m' => 180, '1y' => 365];
        $days = $rangeMap[$rangeKey] ?? null;
        $startDate = $days ? now()->subDays($days)->toDateString() : null;

        // KPI totals
        $scansQuery = ScanLog::query()->where('company_id', $company->id);
        $feedbackQuery = Feedback::query()->where('company_id', $company->id);

        if ($startDate) {
            $scansQuery->whereDate('created_at', '>=', $startDate);
            $feedbackQuery->whereDate('created_at', '>=', $startDate);
        }

        $totalScans = $scansQuery->count();
        $totalFeedback = (clone $feedbackQuery)->count();
        $goodCount = (clone $feedbackQuery)->where('rating', 'good')->count();
        $okCount = (clone $feedbackQuery)->where('rating', 'ok')->count();
        $badCount = (clone $feedbackQuery)->where('rating', 'bad')->count();

        $conversionRate = $totalScans > 0 ? round(($totalFeedback / $totalScans) * 100, 1) : 0;

        // Daily Scans query
        $dailyScansQuery = ScanLog::query()
            ->selectRaw('DATE(created_at) as day')
            ->selectRaw('COUNT(*) as scan_count')
            ->where('company_id', $company->id)
            ->groupBy(DB::raw('DATE(created_at)'));

        if ($startDate) {
            $dailyScansQuery->whereDate('created_at', '>=', $startDate);
        }
        $dailyScans = $dailyScansQuery->pluck('scan_count', 'day');

        // Daily Feedback query
        $dailyFeedbackQuery = Feedback::query()
            ->selectRaw('DATE(created_at) as day')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN rating = 'good' THEN 1 ELSE 0 END) as good_count")
            ->selectRaw("SUM(CASE WHEN rating = 'ok' THEN 1 ELSE 0 END) as ok_count")
            ->selectRaw("SUM(CASE WHEN rating = 'bad' THEN 1 ELSE 0 END) as bad_count")
            ->where('company_id', $company->id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day');

        if ($startDate) {
            $dailyFeedbackQuery->whereDate('created_at', '>=', $startDate);
        }
        $dailyStats = $dailyFeedbackQuery->get();

        // Combine days from both scans and feedback
        $allDays = collect($dailyStats->pluck('day'))
            ->merge($dailyScans->keys())
            ->unique()
            ->sort()
            ->values();

        $chartTimeline = [
            'labels' => $allDays,
            'scans' => $allDays->map(fn ($day) => (int) ($dailyScans[$day] ?? 0)),
            'totals' => $allDays->map(function ($day) use ($dailyStats) {
                $stat = $dailyStats->firstWhere('day', $day);

                return $stat ? (int) $stat->total : 0;
            }),
            'good' => $allDays->map(function ($day) use ($dailyStats) {
                $stat = $dailyStats->firstWhere('day', $day);

                return $stat ? (int) $stat->good_count : 0;
            }),
            'ok' => $allDays->map(function ($day) use ($dailyStats) {
                $stat = $dailyStats->firstWhere('day', $day);

                return $stat ? (int) $stat->ok_count : 0;
            }),
            'bad' => $allDays->map(function ($day) use ($dailyStats) {
                $stat = $dailyStats->firstWhere('day', $day);

                return $stat ? (int) $stat->bad_count : 0;
            }),
        ];

        // Per-employee performance
        $perEmployee = DB::table('employees as e')
            ->leftJoin('feedback as f', function ($join) use ($company, $startDate) {
                $join->on('f.employee_id', '=', 'e.id')
                    ->where('f.company_id', '=', $company->id);
                if ($startDate) {
                    $join->whereDate('f.created_at', '>=', $startDate);
                }
            })
            ->leftJoin('scan_logs as s', function ($join) use ($company, $startDate) {
                $join->on('s.employee_id', '=', 'e.id')
                    ->where('s.company_id', '=', $company->id);
                if ($startDate) {
                    $join->whereDate('s.created_at', '>=', $startDate);
                }
            })
            ->where('e.company_id', $company->id)
            ->select(
                'e.id',
                'e.name',
                DB::raw('COUNT(DISTINCT s.id) as scan_cnt'),
                DB::raw("COALESCE(SUM(CASE WHEN f.rating = 'good' THEN 1 ELSE 0 END), 0) as good_cnt"),
                DB::raw("COALESCE(SUM(CASE WHEN f.rating = 'ok' THEN 1 ELSE 0 END), 0) as ok_cnt"),
                DB::raw("COALESCE(SUM(CASE WHEN f.rating = 'bad' THEN 1 ELSE 0 END), 0) as bad_cnt")
            )
            ->groupBy('e.id', 'e.name')
            ->orderByRaw('(COUNT(DISTINCT s.id) + COALESCE(SUM(CASE WHEN f.rating = \'good\' THEN 1 ELSE 0 END), 0)) DESC')
            ->orderBy('e.name')
            ->get();

        return view('analytics.index', [
            'totalScans' => $totalScans,
            'totalFeedback' => $totalFeedback,
            'goodCount' => $goodCount,
            'okCount' => $okCount,
            'badCount' => $badCount,
            'conversionRate' => $conversionRate,
            'dailyStats' => $dailyStats,
            'perEmployee' => $perEmployee,
            'selectedRange' => $rangeKey,
            'brandName' => $company->name,
            'brandLogoUrl' => $company->logo_url,
            'companies' => $companies->companiesFor(Auth::user()),
            'currentCompany' => $company,
            'chartTimeline' => $chartTimeline,
            'chartEmployees' => [
                'labels' => $perEmployee->pluck('name')->values(),
                'scans' => $perEmployee->pluck('scan_cnt')->map(fn ($v) => (int) $v)->values(),
                'totals' => $perEmployee->map(fn ($e) => (int) $e->good_cnt + (int) $e->ok_cnt + (int) $e->bad_cnt)->values(),
            ],
        ]);
    }
}
