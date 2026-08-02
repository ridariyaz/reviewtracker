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
    .winner-banner {
      background: linear-gradient(135deg, rgba(234, 179, 8, 0.25), rgba(245, 158, 11, 0.15));
      border: 1px solid rgba(234, 179, 8, 0.5);
      border-radius: 16px;
      padding: 16px;
      margin-bottom: 20px;
      box-shadow: 0 10px 25px rgba(234, 179, 8, 0.2);
      animation: pulseGlow 2s infinite;
    }
    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 15px rgba(234, 179, 8, 0.3); }
      50% { box-shadow: 0 0 30px rgba(234, 179, 8, 0.6); }
    }
    .gamification-contest-badge {
      background: rgba(59, 130, 246, 0.15);
      border: 1px solid rgba(59, 130, 246, 0.3);
      color: #60a5fa;
      padding: 8px 14px;
      border-radius: 12px;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 20px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
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

    @if(!empty($isWinner))
      <div class="winner-banner">
        <div style="font-size:1.3rem; font-weight:700; color:#facc15; margin-bottom:4px;">🎉 YOU ARE TODAY'S LUCKY WINNER!</div>
        <div style="font-size:0.95rem; color:#fff; margin-bottom:8px;">You won: <strong>{{ $gamificationReward ?? 'Special Gift Voucher' }}</strong></div>
        <div style="background:rgba(0,0,0,0.3); padding:8px 12px; border-radius:8px; font-family:monospace; font-size:1rem; letter-spacing:1px; color:#fde047; display:inline-block;">
          CLAIM CODE: {{ $winnerCode }}
        </div>
      </div>
    @elseif(!empty($enableGamification))
      <div class="gamification-contest-badge">
        <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M20 12v10H4V12"></path><path d="M22 7H2v5h20V7z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 1 0-5C13 2 12 7 12 7z"></path></svg>
        <span>Review Contest: Every {{ $gamificationInterval ?? 50 }}th reviewer wins {{ $gamificationReward ?? 'a gift' }}!</span>
      </div>
    @endif

    <h1>{{ $txt['headline'] }}</h1>
    <p>{{ $txt['subheadline'] }}</p>

    <div class="rating-options">
      <a class="btn-option btn-good" href="{{ route('review.good', $employeeId) }}">
        <span style="display:flex;align-items:center;gap:10px;">
          <svg viewBox="0 0 24 24" style="width:24px;height:24px;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
          <span>{{ $txt['great'] }}</span>
        </span>
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </a>

      <a class="btn-option btn-ok" href="{{ route('review.ok', $employeeId) }}">
        <span style="display:flex;align-items:center;gap:10px;">
          <svg viewBox="0 0 24 24" style="width:24px;height:24px;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="15" x2="16" y2="15"></line><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
          <span>{{ $txt['ok'] }}</span>
        </span>
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </a>

      <a class="btn-option btn-bad" href="{{ route('review.bad', $employeeId) }}">
        <span style="display:flex;align-items:center;gap:10px;">
          <svg viewBox="0 0 24 24" style="width:24px;height:24px;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"></circle><path d="M16 16s-1.5-2-4-2-4 2-4 2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
          <span>{{ $txt['bad'] }}</span>
        </span>
        <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
      </a>
    </div>
  </div>
</body>
</html>
