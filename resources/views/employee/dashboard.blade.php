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
    <!-- Employee Quick Guide Banner -->
    <div class="card" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #bfdbfe;">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
        <div style="width:36px; height:36px; border-radius:999px; background:#2563eb; color:#fff; font-size:18px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">📱</div>
        <div>
          <h3 style="margin:0; font-size:16px; color:#1e3a8a;">How to Use Your Review QR</h3>
          <div style="font-size:12px; color:#1e40af; margin-top:2px;">Follow these 3 easy steps to start capturing Google Reviews:</div>
        </div>
      </div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:10px; margin-top:12px;">
        <div style="background:#ffffff; padding:10px 14px; border-radius:10px; font-size:12px; border:1px solid #dbeafe;">
          <strong style="color:#2563eb;">1. Open or Save QR:</strong> Tap <em>"Fullscreen View"</em> below or save the QR image to your phone's photo library.
        </div>
        <div style="background:#ffffff; padding:10px 14px; border-radius:10px; font-size:12px; border:1px solid #dbeafe;">
          <strong style="color:#2563eb;">2. Show to Customer:</strong> Ask happy customers at checkout or tables to point their phone camera at your QR.
        </div>
        <div style="background:#ffffff; padding:10px 14px; border-radius:10px; font-size:12px; border:1px solid #dbeafe;">
          <strong style="color:#2563eb;">3. Watch Yourself Rank:</strong> Happy customers get routed straight to Google. Track your stats & rank on the leaderboard!
        </div>
      </div>
    </div>

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
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <h2 style="margin:0;">Your performance & Achievements</h2>
        @php
          $myRank = $leaderboard->search(fn($e) => $e->id === $employee->id);
          $rankNum = $myRank !== false ? $myRank + 1 : null;
        @endphp
        @if($rankNum)
          <span style="padding:4px 12px; border-radius:999px; font-weight:700; font-size:12px; background:#fefce8; border:1px solid #fde047; color:#a16207;">
            🏅 Rank #{{ $rankNum }} in Team
          </span>
        @endif
      </div>

      <div style="display:flex; gap:10px; margin-top:14px; flex-wrap:wrap;">
        @if($rankNum === 1)
          <span style="padding:6px 14px; border-radius:999px; font-size:12px; font-weight:700; background:linear-gradient(135deg, #fef08a, #eab308); color:#713f12; box-shadow:0 2px 8px rgba(234,179,8,0.3);">
            🏆 Top Performer of the Month
          </span>
        @endif

        @if($employee->good_count > 0)
          <span style="padding:6px 14px; border-radius:999px; font-size:12px; font-weight:700; background:linear-gradient(135deg, #dcfce7, #22c55e); color:#14532d; box-shadow:0 2px 8px rgba(34,197,94,0.3);">
            ⭐ 5-Star Champion ({{ $employee->good_count }} Google Reviews)
          </span>
        @endif

        @if($employee->scans >= 5)
          <span style="padding:6px 14px; border-radius:999px; font-size:12px; font-weight:700; background:linear-gradient(135deg, #e0e7ff, #6366f1); color:#1e1b4b; box-shadow:0 2px 8px rgba(99,102,241,0.3);">
            🚀 High Engagement ({{ $employee->scans }} Scans)
          </span>
        @endif
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap:12px; margin-top:16px;">
        <div style="background:#f8fafc; padding:12px; border-radius:12px; border:1px solid #e2e8f0; text-align:center;">
          <div style="font-size:11px; text-transform:uppercase; font-weight:600; color:#64748b;">Total Scans</div>
          <div style="font-size:24px; font-weight:700; color:#0f172a; margin-top:2px;">{{ $employee->scans }}</div>
        </div>
        <div style="background:#f0fdf4; padding:12px; border-radius:12px; border:1px solid #bbf7d0; text-align:center;">
          <div style="font-size:11px; text-transform:uppercase; font-weight:600; color:#166534;">Good (Google)</div>
          <div style="font-size:24px; font-weight:700; color:#15803d; margin-top:2px;">{{ $employee->good_count }}</div>
        </div>
        <div style="background:#fefce8; padding:12px; border-radius:12px; border:1px solid #fef08a; text-align:center;">
          <div style="font-size:11px; text-transform:uppercase; font-weight:600; color:#854d0e;">Private OK</div>
          <div style="font-size:24px; font-weight:700; color:#a16207; margin-top:2px;">{{ $employee->ok_count }}</div>
        </div>
        <div style="background:#fef2f2; padding:12px; border-radius:12px; border:1px solid #fecaca; text-align:center;">
          <div style="font-size:11px; text-transform:uppercase; font-weight:600; color:#991b1b;">Private Bad</div>
          <div style="font-size:24px; font-weight:700; color:#b91c1c; margin-top:2px;">{{ $employee->bad_count }}</div>
        </div>
      </div>
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
