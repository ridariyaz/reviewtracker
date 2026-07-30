<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'ReviewTracker')</title>
  <style>
    :root {
      --primary: #0d6efd;
      --primary-dark: #0b5ed7;
      --bg: #f3f5fb;
      --card-bg: #ffffff;
      --border-soft: #e2e6f0;
      --text-main: #111827;
      --text-muted: #6b7280;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: var(--bg);
      color: var(--text-main);
    }
    .topbar {
      background: #020617;
      color: #e5e7eb;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 1px 4px rgba(15, 23, 42, 0.4);
      flex-wrap: wrap;
    }
    .brand { display: flex; align-items: center; gap: 10px; font-weight: 600; }
    .brand-badge {
      width: 28px; height: 28px; border-radius: 999px;
      background: radial-gradient(circle at 30% 0, #38bdf8, #4f46e5);
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; font-weight: 700; color: #e5e7eb;
    }
    .brand-sub { font-size: 11px; color: #9ca3af; }
    .top-nav { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .nav-link {
      padding: 6px 12px; border-radius: 999px; border: 1px solid transparent;
      color: #cbd5f5; font-size: 14px; text-decoration: none;
    }
    .nav-link:hover { border-color: #4f46e5; background: rgba(79, 70, 229, 0.12); }
    .btn-outline { border-color: #4b5563; }
    .btn-outline:hover { border-color: #ef4444; background: rgba(239, 68, 68, 0.15); }
    .page { max-width: 1120px; margin: 24px auto 40px; padding: 0 16px; }
    .page-header { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
    .page-title { font-size: 24px; font-weight: 600; }
    .page-subtitle { margin-top: 4px; font-size: 14px; color: var(--text-muted); }
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      padding: 8px 14px; border-radius: 999px; border: 1px solid transparent;
      font-size: 14px; font-weight: 500; cursor: pointer; background: var(--primary);
      color: #fff; text-decoration: none;
    }
    .btn:hover { background: var(--primary-dark); }
    .btn-secondary { background: #111827; }
    .btn-secondary:hover { background: #030712; }
    .card {
      background: var(--card-bg); border-radius: 14px; padding: 18px 20px 20px;
      box-shadow: 0 14px 32px rgba(15, 23, 42, 0.16); border: 1px solid rgba(148, 163, 184, 0.25);
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
      <div class="brand-badge">R</div>
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
