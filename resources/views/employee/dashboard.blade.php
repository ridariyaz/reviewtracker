<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Performance · {{ $brandName }}</title>
  <style>
    :root {
      --primary: {{ $brandPrimaryColor }};
      --bg: #f3f5fb;
      --card-bg: #ffffff;
      --border-soft: #e2e6f0;
      --text-main: #111827;
      --text-muted: #6b7280;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: var(--bg);
      color: var(--text-main);
    }
    .topbar {
      background: {{ $brandSecondaryColor }};
      color: #e5e7eb;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      box-shadow: 0 1px 4px rgba(15, 23, 42, 0.4);
    }
    .page {
      max-width: 1120px;
      margin: 24px auto 40px;
      padding: 0 16px;
    }
    .card {
      background: var(--card-bg);
      border-radius: 14px;
      padding: 18px 20px 20px;
      box-shadow: 0 14px 32px rgba(15, 23, 42, 0.16);
      border: 1px solid rgba(148, 163, 184, 0.25);
      margin-bottom: 18px;
    }
    .muted { color: var(--text-muted); font-size: 13px; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    th { background: #f9fafb; font-weight: 500; color: #4b5563; }
    tr:last-child td { border-bottom: none; }
    .actions { margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap; }
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      padding: 8px 12px; border-radius: 999px; border: none;
      font-size: 12px; font-weight: 500; cursor: pointer;
      background: var(--primary); color: #fff; text-decoration: none;
    }
    .btn-dark { background: #111827; }
    a.logout { color: #e5e7eb; text-decoration: none; }
  </style>
</head>
<body>
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:8px;">
      @if($brandLogoUrl)
        <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }} logo" style="max-height:36px;">
      @endif
      <span>{{ $brandName }}</span>
    </div>
    <div>
      {{ $employee->name }}
      ·
      <form action="{{ route('employee.logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="logout" style="background:none;border:none;padding:0;cursor:pointer;color:#e5e7eb;font:inherit;">Logout</button>
      </form>
    </div>
  </div>

  <div class="page">
    <div class="card">
      <h2>Your QR code</h2>
      <p class="muted">Show this QR to customers to capture feedback.</p>
      <img src="{{ asset('storage/qrcodes/'.$employee->id.'.png') }}" width="160" alt="Your QR code" style="background:#fff;padding:8px;border-radius:12px;">
      <div class="actions">
        <a class="btn" href="{{ asset('storage/qrcodes/'.$employee->id.'.png') }}" download="employee_{{ $employee->id }}_qr.png">Download QR</a>
        <a class="btn btn-dark" href="{{ route('employee.qr') }}">Fullscreen view</a>
      </div>
    </div>

    <div class="card">
      <h2>Your performance</h2>
      <p>Total scans: {{ $employee->scans }}</p>
      <p>Good: {{ $employee->good_count }} · OK: {{ $employee->ok_count }} · Bad: {{ $employee->bad_count }}</p>
    </div>

    <div class="card">
      <h2>Your feedback</h2>
      <table>
        <tr>
          <th>Rating</th>
          <th>Comment</th>
          <th>Status</th>
          <th>When</th>
        </tr>
        @forelse($feedbackRows as $row)
        <tr>
          <td>{{ $row->rating }}</td>
          <td>{{ $row->comment ?: '—' }}</td>
          <td>{{ $row->status }}</td>
          <td class="muted">{{ $row->created_at }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="muted">No feedback yet.</td></tr>
        @endforelse
      </table>
    </div>

    <div class="card">
      <h2>Team leaderboard</h2>
      <table>
        <tr>
          <th>#</th>
          <th>Employee</th>
          <th>Total scans</th>
          <th>Good</th>
          <th>OK</th>
          <th>Bad</th>
        </tr>
        @forelse($leaderboard as $row)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $row->name }}@if($row->id === $employee->id) <strong>(you)</strong>@endif</td>
          <td>{{ $row->scans }}</td>
          <td>{{ $row->good_count }}</td>
          <td>{{ $row->ok_count }}</td>
          <td>{{ $row->bad_count }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="muted">No employees yet.</td></tr>
        @endforelse
      </table>
    </div>
  </div>
</body>
</html>
