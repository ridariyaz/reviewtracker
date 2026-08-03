<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Performance · {{ $brandName }}</title>
  <style>
    :root {
      --primary: {{ $brandPrimaryColor }};
      --bg: #0f172a;
      --card-bg: #1e293b;
      --border-soft: rgba(255, 255, 255, 0.1);
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
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
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
      border-bottom: 1px solid var(--border-soft);
    }
    .page {
      max-width: 1120px;
      margin: 24px auto 40px;
      padding: 0 16px;
    }
    .card {
      background: var(--card-bg);
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      border: 1px solid var(--border-soft);
      margin-bottom: 24px;
    }
    .muted { color: var(--text-muted); font-size: 13px; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th, td { padding: 12px 14px; border-bottom: 1px solid var(--border-soft); text-align: left; }
    th { background: rgba(0,0,0,0.2); font-weight: 600; color: #cbd5e1; }
    tr:last-child td { border-bottom: none; }
    .actions { margin-top: 14px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      padding: 10px 18px; border-radius: 10px; border: none;
      font-size: 13px; font-weight: 600; cursor: pointer;
      background: var(--primary); color: #fff; text-decoration: none;
      transition: background 0.2s ease;
    }
    .btn-dark { background: #334155; }
    .btn-dark:hover { background: #475569; }
    .logout-btn {
      background: none; border: none; padding: 0; cursor: pointer; color: #94a3b8; font: inherit;
    }
    .logout-btn:hover { color: #f8fafc; }
    .lang-select {
      padding: 8px 12px;
      border-radius: 10px;
      border: 1px solid var(--border-soft);
      background: #0f172a;
      color: #fff;
      font-size: 13px;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:12px;">
      @if($brandLogoUrl)
        <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }} logo" style="max-height:36px; border-radius:6px;">
      @endif
      <span style="font-weight:700; font-size:1.1rem;">{{ $brandName }}</span>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
      <span style="font-weight:600;">{{ $employee->name }}</span>
      <form action="{{ route('employee.logout') }}" method="POST" style="margin:0;">
        @csrf
        <button type="submit" class="logout-btn">Logout</button>
      </form>
    </div>
  </div>

  <div class="page">
    <!-- QR Language Selector Card -->
    <div class="card">
      <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:16px;">
        <div>
          <h2 style="margin:0; font-size:1.3rem;">Your Customer Review QR</h2>
          <p class="muted" style="margin-top:4px;">Select customer language to generate customized QR code</p>
        </div>
        <div>
          <select id="qrLangSelect" class="lang-select" onchange="updateQrLanguage(this.value)">
            <option value="en" selected>🇬🇧 English</option>
            <option value="ml">🇮🇳 Malayalam (മലയാളം)</option>
            <option value="ar">🇸🇦 Arabic (العربية)</option>
            <option value="hi">🇮🇳 Hindi (हिंदी)</option>
            <option value="bn">🇧🇩 Bengali (বাংলা)</option>
          </select>
        </div>
      </div>

      <div style="display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
        <div style="background:#fff; padding:12px; border-radius:16px; border:1px solid #e2e8f0; text-align:center;">
          <div id="qrContainer">
            @php
              $qrUrl = route('review.show', ['employee' => $employee->id]);
              $svgDataUri = (new \App\Services\QrCodeService())->generateSvgDataUri($qrUrl);
            @endphp
            <img id="qrImage" src="{{ $svgDataUri }}" width="180" height="180" alt="QR Code" style="display:block;">
          </div>
        </div>

        <div style="flex:1; min-width:260px;">
          <div style="font-size:0.85rem; color:#94a3b8; margin-bottom:12px; line-height:1.5;">
            Show this QR code to customers on your mobile screen or print it for your workspace. Scanning it directs customers directly to your review flow.
          </div>
          <div class="actions">
            <a id="btnFullscreen" class="btn" href="{{ route('employee.qr') }}">
              <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
              <span>Fullscreen View</span>
            </a>

            <a class="btn" style="background:#334155;" href="{{ $svgDataUri }}" download="qr-code-{{ \Illuminate\Support\Str::slug($employee->name) }}.svg">
              <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              <span>Download QR</span>
            </a>

            <form action="{{ route('employee.force_win') }}" method="POST" style="margin:0; display:inline-block;">
              @csrf
              <button type="submit" class="btn" style="background:#f59e0b; color:#000; font-weight:800;">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M20 12v10H4V12"></path><path d="M22 7H2v5h20V7z"></path><path d="M12 22V7"></path></svg>
                <span>Make Next Scan Win</span>
              </button>
            </form>
          </div>

          @if(session('success'))
            <div style="margin-top:10px; padding:8px 12px; background:rgba(245,158,11,0.15); border:1px solid #f59e0b; color:#facc15; border-radius:8px; font-size:12px; font-weight:700;">
              {{ session('success') }}
            </div>
          @elseif($employee->force_next_win)
            <div style="margin-top:10px; padding:8px 12px; background:rgba(245,158,11,0.15); border:1px solid #f59e0b; color:#facc15; border-radius:8px; font-size:12px; font-weight:700;">
              Next customer scan is GUARANTEED to win the reward prize!
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Performance Stats Card -->
    <div class="card">
      <h2 style="margin:0 0 16px 0; font-size:1.2rem;">Your Performance Overview</h2>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap:14px;">
        <div style="background:rgba(255,255,255,0.05); padding:16px; border-radius:12px; border:1px solid var(--border-soft); text-align:center;">
          <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#94a3b8;">Total Scans</div>
          <div style="font-size:26px; font-weight:700; color:#3b82f6; margin-top:4px;">{{ $employee->scans }}</div>
        </div>
        <div style="background:rgba(34,197,94,0.1); padding:16px; border-radius:12px; border:1px solid rgba(34,197,94,0.2); text-align:center;">
          <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#4ade80;">Good (Google)</div>
          <div style="font-size:26px; font-weight:700; color:#22c55e; margin-top:4px;">{{ $employee->good_count }}</div>
        </div>
        <div style="background:rgba(234,179,8,0.1); padding:16px; border-radius:12px; border:1px solid rgba(234,179,8,0.2); text-align:center;">
          <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#facc15;">Private OK</div>
          <div style="font-size:26px; font-weight:700; color:#eab308; margin-top:4px;">{{ $employee->ok_count }}</div>
        </div>
        <div style="background:rgba(239,68,68,0.1); padding:16px; border-radius:12px; border:1px solid rgba(239,68,68,0.2); text-align:center;">
          <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#f87171;">Private Bad</div>
          <div style="font-size:26px; font-weight:700; color:#ef4444; margin-top:4px;">{{ $employee->bad_count }}</div>
        </div>
      </div>
    </div>

    <!-- Team Leaderboard Card -->
    <div class="card">
      <h2 style="margin:0 0 16px 0; font-size:1.2rem;">Team Leaderboard</h2>
      <table>
        <tr>
          <th>#</th>
          <th>Employee</th>
          <th>Total Scans</th>
          <th>Google Reviews</th>
        </tr>
        @forelse($leaderboard as $row)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $row->name }}@if($row->id === $employee->id) <strong>(You)</strong>@endif</td>
          <td>{{ $row->scans }}</td>
          <td>{{ $row->good_count }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="muted">No data available.</td></tr>
        @endforelse
      </table>
    </div>
  </div>

  <script>
    const baseUrl = "{{ route('review.show', ['employee' => $employee->id]) }}";
    const fullscreenBase = "{{ route('employee.qr') }}";

    function updateQrLanguage(lang) {
      const targetUrl = baseUrl + '?lang=' + lang;
      document.getElementById('btnFullscreen').href = fullscreenBase + '?lang=' + lang;

      // Dynamically fetch SVG QR data URI or update image
      fetch('/api/qr?url=' + encodeURIComponent(targetUrl))
        .then(res => res.text())
        .catch(() => {});
    }
  </script>
</body>
</html>
