<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Customer Review Standee · {{ $employee->name ?? 'Employee' }} · {{ $brandName }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: {{ $brandPrimaryColor }};
      --secondary: {{ $brandSecondaryColor }};
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: radial-gradient(ellipse at top, var(--secondary), #020617 80%);
      color: #ffffff;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      padding: 24px 16px;
      -webkit-font-smoothing: antialiased;
    }

    .top-actions {
      position: fixed;
      top: 20px;
      left: 0; right: 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 540px;
      margin: 0 auto;
      padding: 0 16px;
      z-index: 100;
    }
    .action-btn {
      background: rgba(15, 23, 42, 0.85);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #ffffff;
      padding: 10px 18px;
      border-radius: 999px;
      font-size: 0.85rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      backdrop-filter: blur(12px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .action-btn:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-1px);
    }
    .btn-print {
      background: var(--primary);
      border-color: transparent;
      box-shadow: 0 8px 20px rgba(13, 110, 253, 0.35);
    }
    .btn-print:hover {
      opacity: 0.92;
    }

    .standee-card {
      width: 100%;
      max-width: 460px;
      background: #ffffff;
      color: #0f172a;
      border-radius: 28px;
      padding: 36px 28px;
      box-shadow: 0 30px 70px rgba(0, 0, 0, 0.6);
      text-align: center;
      position: relative;
      overflow: hidden;
      margin-top: 60px;
      margin-bottom: 24px;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .card-top-accent {
      height: 8px;
      background: linear-gradient(90deg, var(--primary), #38bdf8);
      position: absolute;
      top: 0; left: 0; right: 0;
    }

    .brand-header {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 20px;
    }
    .brand-logo-img {
      max-height: 48px;
      max-width: 160px;
      object-fit: contain;
    }
    .brand-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.4rem;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.02em;
    }

    .employee-badge {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: #f1f5f9;
      border: 1px solid #e2e8f0;
      padding: 8px 18px;
      border-radius: 999px;
      margin-bottom: 24px;
    }
    .employee-avatar {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: var(--primary);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.85rem;
    }
    .employee-name {
      font-size: 0.9rem;
      font-weight: 700;
      color: #334155;
    }

    .qr-frame {
      background: #f8fafc;
      border: 2px dashed #cbd5e1;
      border-radius: 20px;
      padding: 20px;
      display: inline-block;
      margin-bottom: 24px;
      box-shadow: inset 0 2px 6px rgba(0,0,0,0.03);
    }
    .qr-frame img {
      width: min(70vw, 240px);
      height: min(70vw, 240px);
      display: block;
    }

    .call-to-action-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.35rem;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 6px;
      line-height: 1.25;
    }
    .call-to-action-sub {
      font-size: 0.9rem;
      color: #64748b;
      margin-bottom: 20px;
    }

    .gamification-contest-box {
      background: linear-gradient(135deg, #fefce8, #fef08a);
      border: 1px solid #fde047;
      color: #854d0e;
      padding: 12px 16px;
      border-radius: 14px;
      font-size: 0.85rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .footer-note {
      font-size: 0.75rem;
      color: #94a3b8;
      margin-top: 16px;
    }

    /* Print Styles for A4 / Table Standees */
    @media print {
      body {
        background: #ffffff !important;
        color: #000000 !important;
        padding: 0 !important;
      }
      .top-actions { display: none !important; }
      .standee-card {
        box-shadow: none !important;
        border: 2px solid #000000 !important;
        margin: 0 auto !important;
        max-width: 100% !important;
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body>

  <div class="top-actions">
    <a class="action-btn" href="{{ route('employee.dashboard') }}">
      <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back</span>
    </a>

    <button type="button" class="action-btn btn-print" onclick="window.print()">
      <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
      <span>Print / Download Standee</span>
    </button>
  </div>

  <div class="standee-card">
    <div class="card-top-accent"></div>

    <div class="brand-header">
      @if($brandLogoUrl)
        <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="brand-logo-img">
      @endif
      <span class="brand-title">{{ $brandName }}</span>
    </div>

    @if(!empty($employee))
      <div class="employee-badge">
        <div class="employee-avatar">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
        <span class="employee-name">Assisted by {{ $employee->name }}</span>
      </div>
    @endif

    <div class="qr-frame">
      @php
        $lang = request()->query('lang', 'en');
        $targetUrl = route('review.show', ['employee' => $employee->id ?? 1]).'?lang='.$lang;
        $svgDataUri = (new \App\Services\QrCodeService())->generateSvgDataUri($targetUrl);
      @endphp
      <img src="{{ $svgDataUri }}" alt="Scan to Review">
    </div>

    <div class="call-to-action-title">Scan to Rate Your Experience</div>
    <div class="call-to-action-sub">Point your smartphone camera at the QR code above</div>

    @if(!empty($enableGamification))
      <div class="gamification-contest-box">
        <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M20 12v10H4V12"></path><path d="M22 7H2v5h20V7z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
        <span>Review Contest: Every {{ $gamificationInterval ?? 50 }}th scan wins {{ $gamificationReward ?? 'a gift' }}!</span>
      </div>
    @endif

    <div class="footer-note">Powered by ReviewTracker</div>
  </div>

</body>
</html>
