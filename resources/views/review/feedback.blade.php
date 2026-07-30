<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>How was your experience? · {{ $brandName }}</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: radial-gradient(circle at top, {{ $brandSecondaryColor }}, #020617 55%);
      color: #e5e7eb;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 16px;
      text-align: center;
    }
    .card {
      width: 100%; max-width: 460px;
      background: rgba(15, 23, 42, 0.95);
      border-radius: 24px; padding: 26px 26px 28px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65);
      border: 1px solid rgba(148, 163, 184, 0.4);
    }
    .pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 4px 10px; border-radius: 999px;
      border: 1px solid rgba(148, 163, 184, 0.5);
      font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em;
      color: #9ca3af; margin-bottom: 10px;
    }
    h1 { font-size: 26px; margin: 0 0 6px; }
    p { margin: 0 0 18px; font-size: 14px; color: #9ca3af; }
    .btn {
      display: block; width: 100%; padding: 16px; margin-top: 10px;
      border-radius: 999px; border: none; font-size: 16px; font-weight: 500;
      cursor: pointer; text-decoration: none;
    }
    .btn-good { background: linear-gradient(135deg, {{ $brandPrimaryColor }}, #16a34a); color: #f9fafb; }
    .btn-ok { background: linear-gradient(135deg, #facc15, #eab308); color: #111827; }
    .btn-bad { background: linear-gradient(135deg, #f97316, #ef4444); color: #fef2f2; }
  </style>
</head>
<body>
  <div class="card">
    @if($brandLogoUrl)
    <div style="margin-bottom:18px;display:flex;justify-content:center;">
      <div style="width:96px;height:96px;border-radius:999px;overflow:hidden;border:2px solid rgba(148,163,184,0.6);background:#020617;">
        <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }} logo" style="width:100%;height:100%;object-fit:cover;">
      </div>
    </div>
    @endif
    <div class="pill"><span>{{ $brandName }}</span></div>
    <h1>How was your experience?</h1>
    <p>Your feedback helps us improve — it only takes a second.</p>
    <a class="btn btn-good" href="{{ route('review.good', $employeeId) }}">Great</a>
    <a class="btn btn-ok" href="{{ route('review.ok', $employeeId) }}">OK</a>
    <a class="btn btn-bad" href="{{ route('review.bad', $employeeId) }}">Not great</a>
  </div>
</body>
</html>
