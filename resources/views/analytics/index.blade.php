@extends('layouts.app')

@section('title', 'Analytics · ReviewTracker')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .analytics-layout { display: grid; gap: 20px; }
  @media (min-width: 900px) {
    .analytics-layout { grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.6fr); }
  }
</style>
@endsection

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Analytics</div>
      <div class="page-subtitle">
        See how many customers are scanning, how they rate you over time, and who is generating the most feedback.
      </div>
      <form method="GET" action="{{ route('analytics') }}" style="margin-top:10px;">
        <label for="range" class="muted" style="margin-right:6px;">Range:</label>
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

  <div class="analytics-layout">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Timeline</div>
          <div class="card-title">Scans and ratings per day</div>
        </div>
      </div>
      <canvas id="dailyChart" style="max-height:260px;margin-bottom:12px;"></canvas>
      <div class="table-wrapper">
        <table>
          <tr>
            <th>Date</th>
            <th>Total</th>
            <th>Good</th>
            <th>OK</th>
            <th>Bad</th>
          </tr>
          @foreach($dailyStats as $row)
          <tr>
            <td>{{ $row->day }}</td>
            <td>{{ $row->total }}</td>
            <td>{{ $row->good_count ?: 0 }}</td>
            <td>{{ $row->ok_count ?: 0 }}</td>
            <td>{{ $row->bad_count ?: 0 }}</td>
          </tr>
          @endforeach
        </table>
      </div>
      @if($dailyStats->isEmpty())
        <p class="muted" style="margin-top:10px;">No feedback yet. Once you scan a QR and submit, numbers will appear here.</p>
      @endif
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Selected range</div>
          <div class="card-title">Per-employee performance</div>
        </div>
      </div>
      <canvas id="employeeChart" style="max-height:260px;margin-bottom:12px;"></canvas>
      <div class="table-wrapper">
        <table>
          <tr>
            <th>Employee</th>
            <th>Good</th>
            <th>OK</th>
            <th>Bad</th>
            <th>Total</th>
          </tr>
          @foreach($perEmployee as $row)
          <tr>
            <td>{{ $row->name }}</td>
            <td>{{ $row->good_cnt }}</td>
            <td>{{ $row->ok_cnt }}</td>
            <td>{{ $row->bad_cnt }}</td>
            <td>{{ $row->good_cnt + $row->ok_cnt + $row->bad_cnt }}</td>
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
      const daily = @json($chartDaily);
      const employees = @json($chartEmployees);

      const dailyCtx = document.getElementById('dailyChart');
      if (dailyCtx && daily.labels.length) {
        new Chart(dailyCtx, {
          type: 'line',
          data: {
            labels: daily.labels,
            datasets: [
              { label: 'Total', data: daily.totals, borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.1)', tension: 0.2 },
              { label: 'Good', data: daily.good, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', tension: 0.2 },
              { label: 'OK', data: daily.ok, borderColor: '#eab308', backgroundColor: 'rgba(234,179,8,0.1)', tension: 0.2 },
              { label: 'Bad', data: daily.bad, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', tension: 0.2 },
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
              { label: 'Total feedback in range', data: employees.totals, backgroundColor: '#0d6efd' }
            ]
          },
          options: {
            indexAxis: 'y',
            plugins: { legend: { display: false } }
          }
        });
      }
    })();
  </script>
@endsection
