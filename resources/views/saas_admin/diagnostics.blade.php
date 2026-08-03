@extends('layouts.app')

@section('title', 'System Health Diagnostics · Super Admin')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">System Health & Diagnostics</div>
    <div class="page-subtitle">Inspect server environment health, database statistics, and system parameters.</div>
  </div>
  <a href="{{ route('saas_admin.index') }}" class="btn btn-secondary">&larr; SaaS Admin Dashboard</a>
</div>

<!-- Server Environment Details -->
<div class="card" style="padding:24px; margin-bottom:24px;">
  <h3 style="font-size:1.15rem; font-weight:700; color:var(--text-heading); margin-bottom:16px;">🖥️ Server Environment</h3>
  
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div style="background:var(--bg-main); padding:16px; border-radius:12px; border:1px solid var(--border-color);">
      <div class="muted" style="font-size:12px; font-weight:700; text-transform:uppercase;">PHP Engine</div>
      <div style="font-size:1.2rem; font-weight:800; color:var(--text-heading); margin-top:4px;">PHP {{ $phpVersion }}</div>
    </div>

    <div style="background:var(--bg-main); padding:16px; border-radius:12px; border:1px solid var(--border-color);">
      <div class="muted" style="font-size:12px; font-weight:700; text-transform:uppercase;">Framework Version</div>
      <div style="font-size:1.2rem; font-weight:800; color:var(--text-heading); margin-top:4px;">Laravel {{ $laravelVersion }}</div>
    </div>

    <div style="background:var(--bg-main); padding:16px; border-radius:12px; border:1px solid var(--border-color);">
      <div class="muted" style="font-size:12px; font-weight:700; text-transform:uppercase;">Database Driver</div>
      <div style="font-size:1.2rem; font-weight:800; color:var(--text-heading); margin-top:4px; text-transform:uppercase;">{{ $dbDriver }}</div>
    </div>

    <div style="background:var(--bg-main); padding:16px; border-radius:12px; border:1px solid var(--border-color);">
      <div class="muted" style="font-size:12px; font-weight:700; text-transform:uppercase;">App Environment</div>
      <div style="font-size:1.2rem; font-weight:800; color:#16a34a; margin-top:4px; text-transform:uppercase;">{{ app()->environment() }}</div>
    </div>
  </div>
</div>

<!-- Database Table Statistics -->
<div class="card" style="padding:24px;">
  <h3 style="font-size:1.15rem; font-weight:700; color:var(--text-heading); margin-bottom:16px;">📊 Database Table Statistics</h3>

  <table class="table" style="width:100%;">
    <thead>
      <tr>
        <th>Database Table</th>
        <th>Total Records</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($tableStats as $table => $count)
        <tr>
          <td style="font-family:monospace; font-weight:700; color:var(--text-heading);">{{ $table }}</td>
          <td style="font-weight:700;">{{ number_format($count) }} records</td>
          <td><span style="color:#16a34a; font-weight:700; font-size:12px;">● Healthy</span></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
