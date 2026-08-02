<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'ReviewTracker')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
  <style>
    :root, html[data-theme="light"] {
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --bg: #f8fafc;
      --card-bg: #ffffff;
      --border-color: #e2e8f0;
      --text-heading: #0f172a;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --input-bg: #f8fafc;
      --table-header-bg: #f1f5f9;
      --badge-bg: #e0e7ff;
      --badge-text: #3730a3;
    }

    html[data-theme="dark"] {
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --bg: #0f172a;
      --card-bg: #1e293b;
      --border-color: rgba(255, 255, 255, 0.1);
      --text-heading: #f8fafc;
      --text-main: #e2e8f0;
      --text-muted: #94a3b8;
      --input-bg: rgba(0, 0, 0, 0.25);
      --table-header-bg: rgba(0, 0, 0, 0.2);
      --badge-bg: rgba(99, 102, 241, 0.2);
      --badge-text: #818cf8;
    }

    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text-main);
      -webkit-font-smoothing: antialiased;
      transition: background 0.25s ease, color 0.25s ease;
    }

    h1, h2, h3, h4, .page-title, .card-title {
      font-family: 'Outfit', 'Inter', sans-serif;
    }

    .topbar {
      background: var(--card-bg);
      color: var(--text-main);
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
      flex-wrap: wrap;
      border-bottom: 1px solid var(--border-color);
      transition: background 0.25s ease;
    }
    .brand { display: flex; align-items: center; gap: 12px; font-weight: 600; }
    .brand-badge {
      width: 36px; height: 36px; border-radius: 10px;
      background: linear-gradient(135deg, #2563eb, #4f46e5);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 700; color: #ffffff;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    }
    .brand-sub { font-size: 11px; color: var(--text-muted); }
    .top-nav { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    
    .nav-link {
      padding: 8px 14px;
      border-radius: 10px;
      border: 1px solid transparent;
      color: var(--text-main);
      font-size: 13px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .nav-link svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      stroke-width: 2;
      fill: none;
    }
    .nav-link:hover {
      border-color: var(--border-color);
      background: var(--input-bg);
      color: var(--text-heading);
    }
    .nav-badge {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 18px; height: 18px; padding: 0 5px; border-radius: 999px;
      background: #ef4444; color: #ffffff; font-size: 11px; font-weight: 700;
      margin-left: 4px;
    }

    .theme-toggle-btn {
      background: var(--input-bg);
      border: 1px solid var(--border-color);
      color: var(--text-main);
      padding: 7px 14px;
      border-radius: 20px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 600;
      transition: all 0.2s ease;
    }
    .theme-toggle-btn:hover {
      border-color: var(--primary);
      color: var(--text-heading);
    }

    .page { max-width: 1160px; margin: 24px auto 40px; padding: 0 16px; }
    .page-header { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 24px; flex-wrap: wrap; }
    .page-title { font-size: 26px; font-weight: 700; color: var(--text-heading); letter-spacing: -0.02em; }
    .page-subtitle { margin-top: 4px; font-size: 14px; color: var(--text-muted); }
    
    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 6px;
      padding: 10px 18px; border-radius: 10px; border: 1px solid transparent;
      font-size: 14px; font-weight: 600; cursor: pointer; background: var(--primary);
      color: #fff; text-decoration: none; transition: all 0.2s ease;
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }
    .btn:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-secondary { background: #334155; color: #ffffff; }
    .btn-secondary:hover { background: #1e293b; }

    .card {
      background: var(--card-bg); border-radius: 16px; padding: 22px 24px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); border: 1px solid var(--border-color);
      transition: background 0.25s ease, border-color 0.25s ease;
    }
    .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .card-title { font-size: 17px; font-weight: 700; color: var(--text-heading); }
    .card-kicker { font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); font-weight: 700; }

    .input, select, textarea {
      padding: 10px 14px; font-size: 14px; border-radius: 10px;
      border: 1px solid var(--border-color); background: var(--input-bg);
      color: var(--text-main); width: 100%;
    }
    .table-wrapper { margin-top: 6px; border-radius: 12px; border: 1px solid var(--border-color); overflow: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--border-color); vertical-align: middle; color: var(--text-main); }
    th { background: var(--table-header-bg); font-weight: 600; color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
    tr:last-child td { border-bottom: none; }
    .text-right { text-align: right; }
    .pill {
      padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600;
      background: var(--badge-bg); color: var(--badge-text); display: inline-flex; align-items: center; gap: 4px;
    }
    .muted { color: var(--text-muted); font-size: 13px; }

    /* Modal System */
    .modal-backdrop {
      position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(6px); display: flex; align-items: center;
      justify-content: center; z-index: 1000; opacity: 0; pointer-events: none;
      transition: opacity 0.2s ease; padding: 16px;
    }
    .modal-backdrop.active { opacity: 1; pointer-events: auto; }
    .modal-box {
      background: var(--card-bg); color: var(--text-main); width: 100%; max-width: 480px; border-radius: 20px;
      padding: 26px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
      transform: scale(0.95); transition: transform 0.2s ease;
      border: 1px solid var(--border-color);
    }
    .modal-backdrop.active .modal-box { transform: scale(1); }
    .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .modal-title { font-size: 18px; font-weight: 700; color: var(--text-heading); margin: 0; }
    .modal-close { background: var(--input-bg); border: none; width: 30px; height: 30px; border-radius: 999px; font-size: 16px; cursor: pointer; color: var(--text-muted); }
    .modal-close:hover { color: var(--text-heading); }
  </style>
  @yield('styles')
</head>
<body>
  @hasSection('topbar')
    @yield('topbar')
  @else
  <div class="topbar">
    <div class="brand">
      <div class="brand-badge">{{ strtoupper(substr($brandName ?? config('app.name'), 0, 1)) }}</div>
      <div>
        <div style="color: var(--text-heading);">{{ $brandName ?? config('app.name') }}</div>
        <div class="brand-sub">Employee Review Accelerator</div>
      </div>
    </div>
    <div class="top-nav">
      <a class="nav-link" href="{{ route('admin') }}">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        <span>Dashboard</span>
      </a>
      <a class="nav-link" href="{{ route('employees.index') }}">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <span>Employees</span>
      </a>
      <a class="nav-link" href="{{ route('feedback.index') }}">
        <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        <span>Feedback</span>
        @if(($unresolvedFeedbackCount ?? 0) > 0)
          <span class="nav-badge">{{ $unresolvedFeedbackCount }}</span>
        @endif
      </a>
      <a class="nav-link" href="{{ route('analytics') }}">
        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        <span>Analytics</span>
      </a>
      <a class="nav-link" href="{{ route('settings.index') }}">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        <span>Settings</span>
      </a>

      <button type="button" class="theme-toggle-btn" onclick="toggleAppTheme()">
        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
        <span id="appThemeText">Dark Mode</span>
      </button>
    </div>
  </div>
  @endif

  <div class="page">
    @yield('content')
  </div>

  <script>
    function applyTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      const btnText = document.getElementById('appThemeText');
      if (btnText) {
        btnText.textContent = (theme === 'dark') ? 'Light Mode' : 'Dark Mode';
      }
    }

    function toggleAppTheme() {
      const current = document.documentElement.getAttribute('data-theme') || 'light';
      const next = (current === 'dark') ? 'light' : 'dark';
      localStorage.setItem('app_theme', next);
      applyTheme(next);
    }

    const storedTheme = localStorage.getItem('app_theme') || 'light';
    applyTheme(storedTheme);
  </script>

  @yield('scripts')
</body>
</html>
