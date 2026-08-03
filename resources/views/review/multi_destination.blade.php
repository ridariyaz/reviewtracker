<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Thank You! · {{ $brandName }}</title>
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
      display: flex; align-items: center; justify-content: center;
      padding: 20px; text-align: center;
      -webkit-font-smoothing: antialiased;
    }
    .card {
      width: 100%; max-width: 480px;
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(16px);
      border-radius: 24px; padding: 32px 28px;
      box-shadow: 0 25px 60px rgba(0,0,0,0.65);
      border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .brand-logo {
      width: 80px; height: 80px; border-radius: 999px;
      overflow: hidden; border: 3px solid rgba(255,255,255,0.2);
      background: #020617; margin: 0 auto 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }
    .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
    .pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 5px 14px; border-radius: 999px;
      background: rgba(34, 197, 94, 0.15);
      border: 1px solid rgba(34, 197, 94, 0.3);
      font-size: 12px; font-weight: 600; letter-spacing: 0.05em;
      color: #4ade80; margin-bottom: 14px; text-transform: uppercase;
    }
    h1 { font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 8px; color: #ffffff; }
    p { margin: 0 0 24px; font-size: 14px; color: #94a3b8; line-height: 1.5; }
    
    .destinations-list { display: flex; flex-direction: column; gap: 12px; }
    .dest-btn {
      display: flex; align-items: center; justify-content: space-between;
      padding: 16px 20px; border-radius: 16px; text-decoration: none;
      color: #ffffff; font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 16px;
      transition: all 0.2s ease; box-shadow: 0 6px 18px rgba(0,0,0,0.3);
      border: 1px solid rgba(255,255,255,0.15);
    }
    .dest-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.5); filter: brightness(1.1); }
    .dest-info { display: flex; align-items: center; gap: 12px; }
    .dest-icon { font-size: 20px; }
  </style>
</head>
<body>
  <div class="card">
    @if($brandLogoUrl)
    <div class="brand-logo">
      <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }} logo">
    </div>
    @endif
    <div class="pill">🌟 Thank You!</div>
    <h1>Where would you like to review us?</h1>
    <p>Choose one or multiple platforms below to post your review:</p>

    <div class="destinations-list">
      @foreach($destinations as $dest)
        <a class="dest-btn" href="{{ $dest['url'] }}" target="_blank" style="background: {{ $dest['bg'] ?? 'rgba(255,255,255,0.08)' }};">
          <div class="dest-info">
            <span class="dest-icon">{{ $dest['icon'] ?? '🌐' }}</span>
            <span>Post on {{ $dest['name'] }}</span>
          </div>
          <span style="font-size: 18px;">↗</span>
        </a>
      @endforeach
    </div>
  </div>
</body>
</html>
