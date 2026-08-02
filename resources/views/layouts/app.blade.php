<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'ReviewTracker')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --bg: #020617;
      --card-bg: rgba(15, 23, 42, 0.85);
      --border-color: rgba(255, 255, 255, 0.1);
      --text-heading: #f8fafc;
      --text-main: #e2e8f0;
      --text-muted: #94a3b8;
      --input-bg: rgba(0, 0, 0, 0.25);
    }

    [data-theme="light"] {
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --bg: #f8fafc;
      --card-bg: #ffffff;
      --border-color: #e2e8f0;
      --text-heading: #0f172a;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --input-bg: #f1f5f9;
    }

    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text-main);
      -webkit-font-smoothing: antialiased;
      transition: background 0.3s ease, color 0.3s ease;
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
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      flex-wrap: wrap;
      border-bottom: 1px solid var(--border-color);
      backdrop-filter: blur(12px);
    }
    .brand { display: flex; align-items: center; gap: 12px; font-weight: 600; }
    .brand-badge {
      width: 36px; height: 36px; border-radius: 10px;
      background: linear-gradient(135deg, #3b82f6, #6366f1);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 700; color: #ffffff;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
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
      background: rgba(255, 255, 255, 0.08);
      color: var(--text-heading);
    }
    .nav-badge {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 18px; height: 18px; padding: 0 5px; border-radius: 999px;
      background: #ef4444; color: #ffffff; font-size: 11px; font-weight: 700;
      margin-left: 4px;
    }

    .theme-toggle-btn {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid var(--border-color);
      color: var(--text-main);
      padding: 7px 12px;
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
      background: rgba(255, 255, 255, 0.15);
    }

    .page { max-width: 1160px; margin: 24px auto 40px; padding: 0 16px; }
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
        <span id="appThemeText">Dark</span>
      </button>
    </div>
  </div>
  @endif

  <div class="page">
    @yield('content')
  </div>

  <script>
    function toggleAppTheme() {
      const html = document.documentElement;
      const current = html.getAttribute('data-theme') || 'dark';
      const next = (current === 'dark') ? 'light' : 'dark';
      html.setAttribute('data-theme', next);
      document.getElementById('appThemeText').textContent = (next === 'dark') ? 'Dark' : 'Light';
      localStorage.setItem('app_theme', next);
    }
    const storedTheme = localStorage.getItem('app_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', storedTheme);
    if (document.getElementById('appThemeText')) {
      document.getElementById('appThemeText').textContent = (storedTheme === 'dark') ? 'Dark' : 'Light';
    }
  </script>

  @yield('scripts')
</body>
</html>
