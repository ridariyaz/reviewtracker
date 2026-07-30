<!DOCTYPE html>
<html dir="{{ $txt['dir'] ?? 'ltr' }}">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $txt['headline'] }} · {{ $brandName }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: radial-gradient(ellipse at top, {{ $brandSecondaryColor }}, #020617 80%);
      color: #e5e7eb;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      text-align: center;
      -webkit-font-smoothing: antialiased;
    }
    .card {
      width: 100%; max-width: 440px;
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(16px);
      border-radius: 24px; padding: 32px 28px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.65);
      border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .brand-logo {
      width: 88px; height: 88px; border-radius: 999px;
      overflow: hidden; border: 3px solid rgba(255,255,255,0.2);
      background: #020617; margin: 0 auto 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }
    .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
    .pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 5px 14px; border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      font-size: 12px; font-weight: 600; letter-spacing: 0.05em;
      color: #cbd5e1; margin-bottom: 14px; text-transform: uppercase;
    }
    h1 { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 700; margin: 0 0 8px; color: #ffffff; }
    p { margin: 0 0 24px; font-size: 14px; color: #94a3b8; line-height: 1.5; }
    
    .rating-options { display: flex; flex-direction: column; gap: 12px; }
    .btn-option {
      display: flex; align-items: center; justify-content: space-between;
      padding: 16px 20px; border-radius: 16px; border: 1px solid transparent;
      font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); text-align: left;
    }
    html[dir="rtl"] .btn-option { text-align: right; }
    .btn-option:hover { transform: translateY(-2px); }
    
    .btn-good {
      background: linear-gradient(135deg, {{ $brandPrimaryColor }}, #16a34a);
      color: #ffffff; box-shadow: 0 8px 20px rgba(22, 163, 74, 0.3);
    }
    .btn-good:hover { box-shadow: 0 12px 28px rgba(22, 163, 74, 0.45); }
    
    .btn-ok {
      background: rgba(250, 204, 21, 0.15);
      border-color: rgba(250, 204, 21, 0.4);
      color: #fef08a;
    }
    .btn-ok:hover { background: rgba(250, 204, 21, 0.25); border-color: #facc15; }
    
    .btn-bad {
      background: rgba(248, 113, 113, 0.12);
      border-color: rgba(248, 113, 113, 0.3);
      color: #fca5a5;
    }
    .btn-bad:hover { background: rgba(248, 113, 113, 0.22); border-color: #f87171; }
    
    .emoji-icon { font-size: 24px; margin-right: 12px; }
    html[dir="rtl"] .emoji-icon { margin-right: 0; margin-left: 12px; }
    .arrow-icon { font-size: 18px; opacity: 0.7; }
    html[dir="rtl"] .arrow-icon { transform: rotate(180deg); }
  </style>
</head>
<body>
  <div class="card">
    @if($brandLogoUrl)
    <div class="brand-logo">
      <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }} logo">
    </div>
    @endif
    <div class="pill"><span>{{ $brandName }}</span></div>
    <h1>{{ $txt['headline'] }}</h1>
    <p>{{ $txt['subheadline'] }}</p>

    <div class="rating-options">
      <a class="btn-option btn-good" href="{{ route('review.good', $employeeId) }}">
        <span style="display:flex;align-items:center;">
          <span class="emoji-icon">😍</span>
          <span>{{ $txt['great'] }}</span>
        </span>
        <span class="arrow-icon">➔</span>
      </a>

      <a class="btn-option btn-ok" href="{{ route('review.ok', $employeeId) }}">
        <span style="display:flex;align-items:center;">
          <span class="emoji-icon">😊</span>
          <span>{{ $txt['ok'] }}</span>
        </span>
        <span class="arrow-icon">➔</span>
      </a>

      <a class="btn-option btn-bad" href="{{ route('review.bad', $employeeId) }}">
        <span style="display:flex;align-items:center;">
          <span class="emoji-icon">🙁</span>
          <span>{{ $txt['bad'] }}</span>
        </span>
        <span class="arrow-icon">➔</span>
      </a>
    </div>
  </div>
</body>
</html>
