@extends('layouts.app')

@section('title', 'Analytics · ReviewTracker')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
  .kpi-card { background: var(--card-bg); border-radius: 14px; padding: 18px 20px; border: 1px solid rgba(148, 163, 184, 0.25); box-shadow: 0 4px 12px rgba(15,23,42,0.06); }
  .kpi-title { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 6px; }
  .kpi-value { font-size: 28px; font-weight: 700; color: var(--text-main); }
  .kpi-subtext { font-size: 12px; color: #10b981; margin-top: 4px; font-weight: 500; }
  
  .funnel-container { background: #f8fafc; border-radius: 12px; padding: 16px 20px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
  .funnel-title { font-size: 14px; font-weight: 600; margin-bottom: 12px; color: #334155; }
  .funnel-bar-bg { height: 12px; background: #e2e8f0; border-radius: 999px; overflow: hidden; display: flex; }
  .funnel-seg-good { background: #22c55e; height: 100%; }
  .funnel-seg-ok { background: #eab308; height: 100%; }
  .funnel-seg-bad { background: #ef4444; height: 100%; }
  .funnel-legend { display: flex; gap: 16px; margin-top: 10px; font-size: 12px; flex-wrap: wrap; }
  .legend-item { display: flex; align-items: center; gap: 6px; }
  .legend-dot { width: 10px; height: 10px; border-radius: 999px; }

  .analytics-layout { display: grid; gap: 20px; }
  @media (min-width: 900px) {
    .analytics-layout { grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.6fr); }
  }
</style>
@endsection

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Analytics & Conversion Funnel</div>
      <div class="page-subtitle">
        Track customer QR scan conversion rates, Google Review redirects, and employee performance.
      </div>
      <form method="GET" action="{{ route('analytics') }}" style="margin-top:12px;">
        <label for="range" class="muted" style="margin-right:6px;">Timeframe:</label>
        <select id="range" name="range" class="input" style="width:auto;display:inline-block;" onchange="this.form.submit()">
          <option value="7d" @selected($selectedRange === '7d')>Last 7 days</option>
          <option value="30d" @selected($selectedRange === '30d')>Last 30 days</option>
          <option value="6m" @selected($selectedRange === '6m')>Last 6 months</option>
          <option value="1y" @selected($selectedRange === '1y')>Last 12 months</option>
          <option value="all" @selected($selectedRange === 'all')>All time</option>
        </select>
      </form>
    </div>
  </div>

  <!-- KPI Overview Grid -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-title">Total QR Scans</div>
      <div class="kpi-value">{{ number_format($totalScans) }}</div>
      <div class="kpi-subtext">📱 Customer QR code scans</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-title">Total Ratings</div>
      <div class="kpi-value">{{ number_format($totalFeedback) }}</div>
      <div class="kpi-subtext">⭐ Ratings completed</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-title">Conversion Rate</div>
      <div class="kpi-value">{{ $conversionRate }}%</div>
      <div class="kpi-subtext">🎯 Scans converting to feedback</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-title">Google Redirects</div>
      <div class="kpi-value" style="color:#16a34a;">{{ number_format($goodCount) }}</div>
      <div class="kpi-subtext">🚀 5-Star Google reviews routed</div>
    </div>
  </div>

  <!-- Conversion Funnel Bar -->
  @if($totalScans > 0)
    @php
      $pctGood = $totalScans > 0 ? round(($goodCount / $totalScans) * 100, 1) : 0;
      $pctOk = $totalScans > 0 ? round(($okCount / $totalScans) * 100, 1) : 0;
      $pctBad = $totalScans > 0 ? round(($badCount / $totalScans) * 100, 1) : 0;
      $pctDrop = max(0, 100 - ($pctGood + $pctOk + $pctBad));
    @endphp
    <div class="funnel-container">
      <div class="funnel-title">Customer Review Funnel Breakdown</div>
      <div class="funnel-bar-bg">
        <div class="funnel-seg-good" style="width: {{ $pctGood }}%;" title="Google Redirects: {{ $pctGood }}%"></div>
        <div class="funnel-seg-ok" style="width: {{ $pctOk }}%;" title="Private OK Feedback: {{ $pctOk }}%"></div>
        <div class="funnel-seg-bad" style="width: {{ $pctBad }}%;" title="Private Bad Feedback: {{ $pctBad }}%"></div>
      </div>
      <div class="funnel-legend">
        <div class="legend-item"><span class="legend-dot" style="background:#22c55e;"></span> Google Redirects (Good): <strong>{{ $goodCount }} ({{ $pctGood }}%)</strong></div>
        <div class="legend-item"><span class="legend-dot" style="background:#eab308;"></span> Private OK Feedback: <strong>{{ $okCount }} ({{ $pctOk }}%)</strong></div>
        <div class="legend-item"><span class="legend-dot" style="background:#ef4444;"></span> Private Bad Feedback: <strong>{{ $badCount }} ({{ $pctBad }}%)</strong></div>
        <div class="legend-item"><span class="legend-dot" style="background:#cbd5e1;"></span> Scanned Only: <strong>{{ max(0, $totalScans - $totalFeedback) }} ({{ round($pctDrop, 1) }}%)</strong></div>
      </div>
    </div>
  @endif

  <div class="analytics-layout">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Scan & Rating Trends</div>
          <div class="card-title">Daily Scans vs. Submissions</div>
        </div>
      </div>
      <canvas id="dailyChart" style="max-height:260px;margin-bottom:12px;"></canvas>
      <div class="table-wrapper">
        <table>
          <tr>
            <th>Date</th>
            <th>Total Feedback</th>
            <th>Good</th>
            <th>OK</th>
            <th>Bad</th>
          </tr>
          @foreach($dailyStats as $row)
          <tr>
            <td>{{ $row->day }}</td>
            <td><strong>{{ $row->total }}</strong></td>
            <td style="color:#16a34a;">{{ $row->good_count ?: 0 }}</td>
            <td style="color:#ca8a04;">{{ $row->ok_count ?: 0 }}</td>
            <td style="color:#dc2626;">{{ $row->bad_count ?: 0 }}</td>
          </tr>
          @endforeach
        </table>
      </div>
      @if($dailyStats->isEmpty())
        <p class="muted" style="margin-top:10px;">No scan or feedback history recorded yet for this period.</p>
      @endif
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Staff Matrix</div>
          <div class="card-title">Per-Employee Scans & Conversion</div>
        </div>
      </div>
      <canvas id="employeeChart" style="max-height:260px;margin-bottom:12px;"></canvas>
      <div class="table-wrapper">
        <table>
          <tr>
            <th>Employee</th>
            <th>Scans</th>
            <th>Good</th>
            <th>OK</th>
            <th>Bad</th>
            <th>Conv. %</th>
          </tr>
          @foreach($perEmployee as $row)
          @php
            $empScans = (int) $row->scan_cnt;
            $empFeedback = (int) $row->good_cnt + (int) $row->ok_cnt + (int) $row->bad_cnt;
            $empConv = $empScans > 0 ? round(($empFeedback / $empScans) * 100, 1) : ($empFeedback > 0 ? 100 : 0);
          @endphp
          <tr>
            <td><strong>{{ $row->name }}</strong></td>
            <td>{{ $empScans }}</td>
            <td style="color:#16a34a;">{{ $row->good_cnt }}</td>
            <td style="color:#ca8a04;">{{ $row->ok_cnt }}</td>
            <td style="color:#dc2626;">{{ $row->bad_cnt }}</td>
            <td><span class="pill">{{ $empConv }}%</span></td>
          </tr>
          @endforeach
        </table>
      </div>
      @if($perEmployee->isEmpty())
        <p class="muted" style="margin-top:10px;">No employees or feedback yet.</p>
      @endif
    </div>
  </div>

  <script>
    (function () {
      const timeline = @json($chartTimeline);
      const employees = @json($chartEmployees);

      const dailyCtx = document.getElementById('dailyChart');
      if (dailyCtx && timeline.labels.length) {
        new Chart(dailyCtx, {
          type: 'line',
          data: {
            labels: timeline.labels,
            datasets: [
              { label: 'Raw Scans', data: timeline.scans, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', tension: 0.2, borderWidth: 2 },
              { label: 'Total Feedback', data: timeline.totals, borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.1)', tension: 0.2 },
              { label: 'Good (Google)', data: timeline.good, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', tension: 0.2 },
              { label: 'OK', data: timeline.ok, borderColor: '#eab308', backgroundColor: 'rgba(234,179,8,0.1)', tension: 0.2 },
              { label: 'Bad', data: timeline.bad, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', tension: 0.2 },
            ]
          },
          options: {
            plugins: { legend: { display: true, position: 'bottom' } },
            scales: { x: { ticks: { maxRotation: 45, minRotation: 0 } } }
          }
        });
      }

      const empCtx = document.getElementById('employeeChart');
      if (empCtx && employees.labels.length) {
        new Chart(empCtx, {
          type: 'bar',
          data: {
            labels: employees.labels,
            datasets: [
              { label: 'QR Scans', data: employees.scans, backgroundColor: '#6366f1' },
              { label: 'Feedback Received', data: employees.totals, backgroundColor: '#0d6efd' }
            ]
          },
          options: {
            indexAxis: 'y',
            plugins: { legend: { display: true, position: 'bottom' } }
          }
        });
      }
    })();
  </script>
@endsection
