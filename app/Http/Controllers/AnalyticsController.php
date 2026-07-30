<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Services\CompanyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Admin analytics: daily rating trends and per-employee totals for a selected date range.
 * Chart datasets are prepared here and rendered with Chart.js in the Blade view.
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

        $dailyQuery = Feedback::query()
            ->selectRaw('DATE(created_at) as day')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN rating = 'good' THEN 1 ELSE 0 END) as good_count")
            ->selectRaw("SUM(CASE WHEN rating = 'ok' THEN 1 ELSE 0 END) as ok_count")
            ->selectRaw("SUM(CASE WHEN rating = 'bad' THEN 1 ELSE 0 END) as bad_count")
            ->where('company_id', $company->id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day');

        if ($startDate) {
            $dailyQuery->whereDate('created_at', '>=', $startDate);
        }

        $perEmployee = DB::table('employees as e')
            ->leftJoin('feedback as f', function ($join) use ($company, $startDate) {
                $join->on('f.employee_id', '=', 'e.id')
                    ->where('f.company_id', '=', $company->id);
                if ($startDate) {
                    $join->whereDate('f.created_at', '>=', $startDate);
                }
            })
            ->where('e.company_id', $company->id)
            ->select(
                'e.id',
                'e.name',
                DB::raw("COALESCE(SUM(CASE WHEN f.rating = 'good' THEN 1 ELSE 0 END), 0) as good_cnt"),
                DB::raw("COALESCE(SUM(CASE WHEN f.rating = 'ok' THEN 1 ELSE 0 END), 0) as ok_cnt"),
                DB::raw("COALESCE(SUM(CASE WHEN f.rating = 'bad' THEN 1 ELSE 0 END), 0) as bad_cnt")
            )
            ->groupBy('e.id', 'e.name')
            ->orderByRaw('(COALESCE(SUM(CASE WHEN f.rating = \'good\' THEN 1 ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN f.rating = \'ok\' THEN 1 ELSE 0 END), 0) + COALESCE(SUM(CASE WHEN f.rating = \'bad\' THEN 1 ELSE 0 END), 0)) DESC')
            ->orderBy('e.name')
            ->get();

        $dailyStats = $dailyQuery->get();

        return view('analytics.index', [
            'dailyStats' => $dailyStats,
            'perEmployee' => $perEmployee,
            'selectedRange' => $rangeKey,
            'brandName' => $company->name,
            'brandLogoUrl' => $company->logo_url,
            'companies' => $companies->companiesFor(Auth::user()),
            'currentCompany' => $company,
            'chartDaily' => [
                'labels' => $dailyStats->pluck('day')->values(),
                'totals' => $dailyStats->pluck('total')->map(fn ($v) => (int) $v)->values(),
                'good' => $dailyStats->pluck('good_count')->map(fn ($v) => (int) $v)->values(),
                'ok' => $dailyStats->pluck('ok_count')->map(fn ($v) => (int) $v)->values(),
                'bad' => $dailyStats->pluck('bad_count')->map(fn ($v) => (int) $v)->values(),
            ],
            'chartEmployees' => [
                'labels' => $perEmployee->pluck('name')->values(),
                'totals' => $perEmployee->map(fn ($e) => (int) $e->good_cnt + (int) $e->ok_cnt + (int) $e->bad_cnt)->values(),
            ],
        ]);
    }
}
