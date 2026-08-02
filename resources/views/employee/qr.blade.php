<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fullscreen QR · {{ config('app.name') }}</title>
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: #020617;
      color: #fff;
      font-family: system-ui, -apple-system, sans-serif;
    }
    .qr-card {
      background: #ffffff;
      padding: 24px;
      border-radius: 24px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
      text-align: center;
    }
    .qr-card img {
      width: min(80vw, 360px);
      height: min(80vw, 360px);
      display: block;
    }
    .back-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      color: #93c5fd;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 6px;
      background: rgba(255, 255, 255, 0.1);
      padding: 8px 16px;
      border-radius: 20px;
      backdrop-filter: blur(8px);
    }
    .back-btn:hover { background: rgba(255, 255, 255, 0.2); }
  </style>
</head>
<body>
  <a class="back-btn" href="{{ route('employee.dashboard') }}">
    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
    <span>Back to Dashboard</span>
  </a>

  <div class="qr-card">
    @php
      $lang = request()->query('lang', 'en');
      $targetUrl = route('review.show', ['employee' => $employeeId]).'?lang='.$lang;
      $svgDataUri = (new \App\Services\QrCodeService())->generateSvgDataUri($targetUrl);
    @endphp
    <img src="{{ $svgDataUri }}" alt="Employee QR Code">
  </div>
</body>
</html>
