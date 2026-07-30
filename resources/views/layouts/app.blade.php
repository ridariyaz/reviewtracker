  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #0d6efd;
      --primary-dark: #0b5ed7;
      --bg: #f8fafc;
      --card-bg: #ffffff;
      --border-soft: #e2e8f0;
      --text-main: #0f172a;
      --text-muted: #64748b;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: var(--bg);
      color: var(--text-main);
      -webkit-font-smoothing: antialiased;
    }
    h1, h2, h3, h4, .page-title, .card-title {
      font-family: 'Outfit', 'Inter', sans-serif;
    }
    .topbar {
      background: #020617;
      color: #e5e7eb;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 4px 20px rgba(2, 6, 23, 0.5);
      flex-wrap: wrap;
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .brand { display: flex; align-items: center; gap: 10px; font-weight: 600; }
    .brand-badge {
      width: 32px; height: 32px; border-radius: 999px;
      background: linear-gradient(135deg, #38bdf8, #4f46e5);
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; font-weight: 700; color: #ffffff;
      box-shadow: 0 2px 10px rgba(56, 189, 248, 0.4);
    }
    .brand-sub { font-size: 11px; color: #9ca3af; }
    .top-nav { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .nav-link {
      padding: 6px 14px; border-radius: 999px; border: 1px solid transparent;
      color: #cbd5f5; font-size: 13px; font-weight: 500; text-decoration: none;
      transition: all 0.2s ease;
    }
    .nav-link:hover { border-color: #4f46e5; background: rgba(79, 70, 229, 0.18); color: #fff; transform: translateY(-1px); }
    .btn-outline { border-color: #4b5563; }
    .btn-outline:hover { border-color: #ef4444; background: rgba(239, 68, 68, 0.15); }
    .page { max-width: 1160px; margin: 24px auto 40px; padding: 0 16px; }
    .page-header { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 24px; flex-wrap: wrap; }
    .page-title { font-size: 26px; font-weight: 700; letter-spacing: -0.02em; }
    .page-subtitle { margin-top: 4px; font-size: 14px; color: var(--text-muted); }
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      padding: 9px 16px; border-radius: 999px; border: 1px solid transparent;
      font-size: 14px; font-weight: 600; cursor: pointer; background: var(--primary);
      color: #fff; text-decoration: none; transition: all 0.2s ease;
      box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    }
    .btn:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(13, 110, 253, 0.35); }
    .btn-secondary { background: #0f172a; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2); }
    .btn-secondary:hover { background: #1e293b; box-shadow: 0 6px 16px rgba(15, 23, 42, 0.3); }
    .card {
      background: var(--card-bg); border-radius: 16px; padding: 22px 24px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); border: 1px solid rgba(226, 232, 240, 0.8);
      transition: all 0.2s ease;
    }
    .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .card-title { font-size: 16px; font-weight: 600; }
    .card-kicker { font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; color: var(--text-muted); }
    .input, select, textarea {
      padding: 10px 12px; font-size: 14px; border-radius: 999px;
      border: 1px solid var(--border-soft); background: #f9fafb; width: 100%;
    }
    textarea { border-radius: 12px; }
    .input:focus, select:focus, textarea:focus {
      outline: none; border-color: var(--primary); box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.35); background: #fff;
    }
    .table-wrapper { margin-top: 6px; border-radius: 12px; border: 1px solid var(--border-soft); overflow: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
    th { background: #f9fafb; font-weight: 500; color: #4b5563; }
    tr:last-child td { border-bottom: none; }
    .text-right { text-align: right; }
    .pill {
      padding: 4px 10px; border-radius: 999px; font-size: 12px;
      background: #dbeafe; color: #1d4ed8; display: inline-flex; align-items: center; gap: 4px;
    }
    .muted { color: var(--text-muted); font-size: 13px; }
    .field { margin-bottom: 12px; }
    label { display: block; font-size: 13px; margin-bottom: 4px; color: #374151; }
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
        <div>{{ $brandName ?? config('app.name') }}</div>
        <div class="brand-sub">Employee QR review funnel</div>
      </div>
    </div>
    <div class="top-nav">
      <a class="nav-link" href="{{ route('admin') }}">Dashboard</a>
      <a class="nav-link" href="{{ route('employees.index') }}">Employees</a>
      <a class="nav-link" href="{{ route('feedback.index') }}">Feedback</a>
      <a class="nav-link" href="{{ route('analytics') }}">Analytics</a>
      <a class="nav-link" href="{{ route('companies.index') }}">Company settings</a>
      <a class="nav-link" href="{{ route('help') }}">Help & Guide</a>
      @isset($companies)
        @if($companies->count())
        <form action="{{ route('companies.switch') }}" method="POST" style="margin:0;">
          @csrf
          <select name="company_id" onchange="this.form.submit()" style="padding:6px 10px;border-radius:999px;border:1px solid #374151;background:#0b1220;color:#e5e7eb;">
            @foreach($companies as $c)
              <option value="{{ $c->id }}" @selected(isset($currentCompany) && $c->id === $currentCompany->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </form>
        @endif
      @endisset
      <form action="{{ route('logout') }}" method="POST" style="margin:0;">
        @csrf
        <button type="submit" class="nav-link btn-outline" style="background:transparent;cursor:pointer;">Logout</button>
      </form>
    </div>
  </div>
  @endif

  <div class="page">
    @yield('content')
  </div>
</body>
</html>
