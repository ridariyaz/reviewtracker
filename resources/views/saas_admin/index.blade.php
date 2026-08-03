@extends('layouts.app')

@section('title', 'SaaS Platform Control Center · Super Admin')

@section('content')
<div class="page-header" style="background: linear-gradient(135deg, #0f172a, #1e293b); color:#fff; padding:24px; border-radius:16px; margin-bottom:24px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div>
      <div style="font-size:11px; text-transform:uppercase; letter-spacing:0.12em; color:#38bdf8; font-weight:700; margin-bottom:4px;">Platform Control & Troubleshooting</div>
      <div class="page-title" style="color:#ffffff; font-size:1.8rem; font-weight:800;">SaaS Super Admin Portal</div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-top:4px;">
        Monitor customer accounts, troubleshoot client dashboards, inject custom scripts, and broadcast system announcements.
      </div>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="{{ route('saas_admin.users') }}" class="btn" style="background:#0284c7; color:#fff; border:none;">
        <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        <span>Customer Accounts</span>
      </a>
      <a href="{{ route('saas_admin.code') }}" class="btn btn-secondary">
        <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
        <span>Code Injector</span>
      </a>
      <a href="{{ route('saas_admin.diagnostics') }}" class="btn btn-secondary">
        <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
        <span>Diagnostics</span>
      </a>
    </div>
  </div>
</div>

<!-- SaaS Overview KPI Cards -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:18px; margin-bottom:28px;">
  <div class="card" style="padding:20px; border-left:4px solid #0284c7;">
    <div class="card-kicker" style="color:#0284c7;">Registered Customer Accounts</div>
    <div style="font-size:2rem; font-weight:800; color:var(--text-heading); margin-top:4px;">{{ $totalUsers }}</div>
    <div class="muted" style="font-size:12px; margin-top:2px;">Active SaaS clients</div>
  </div>

  <div class="card" style="padding:20px; border-left:4px solid #6366f1;">
    <div class="card-kicker" style="color:#6366f1;">Total Business Brands</div>
    <div style="font-size:2rem; font-weight:800; color:var(--text-heading); margin-top:4px;">{{ $totalCompanies }}</div>
    <div class="muted" style="font-size:12px; margin-top:2px;">Configured company profiles</div>
  </div>

  <div class="card" style="padding:20px; border-left:4px solid #10b981;">
    <div class="card-kicker" style="color:#10b981;">Active Staff Members</div>
    <div style="font-size:2rem; font-weight:800; color:var(--text-heading); margin-top:4px;">{{ $totalEmployees }}</div>
    <div class="muted" style="font-size:12px; margin-top:2px;">Staff with QR standees</div>
  </div>

  <div class="card" style="padding:20px; border-left:4px solid #f59e0b;">
    <div class="card-kicker" style="color:#f59e0b;">Total Customer Scans</div>
    <div style="font-size:2rem; font-weight:800; color:var(--text-heading); margin-top:4px;">{{ $totalScans }}</div>
    <div class="muted" style="font-size:12px; margin-top:2px;">QR scans processed</div>
  </div>

  <div class="card" style="padding:20px; border-left:4px solid #ec4899;">
    <div class="card-kicker" style="color:#ec4899;">Feedback Intercepts</div>
    <div style="font-size:2rem; font-weight:800; color:var(--text-heading); margin-top:4px;">{{ $totalFeedback }}</div>
    <div class="muted" style="font-size:12px; margin-top:2px;">Private internal reviews</div>
  </div>
</div>

<!-- Recent Registered Accounts -->
<div class="card" style="padding:24px; margin-bottom:28px;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
    <div>
      <h3 style="font-size:1.15rem; font-weight:700; color:var(--text-heading);">Recently Registered Customer Accounts</h3>
      <div class="muted" style="font-size:0.85rem;">Click 'Troubleshoot' to log directly into any user dashboard.</div>
    </div>
    <a href="{{ route('saas_admin.users') }}" class="btn btn-secondary" style="font-size:13px; padding:6px 14px;">View All Users &rarr;</a>
  </div>

  <div style="overflow-x:auto;">
    <table class="table" style="width:100%;">
      <thead>
        <tr>
          <th>User Account</th>
          <th>Brands</th>
          <th>Registered</th>
          <th>Status</th>
          <th>Troubleshoot / Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentUsers as $u)
          <tr>
            <td>
              <div style="font-weight:700; color:var(--text-heading);">{{ $u->username }}</div>
              <div class="muted" style="font-size:12px;">{{ $u->email ?: 'No email set' }}</div>
            </td>
            <td>
              <span class="badge" style="background:var(--primary-subtle); color:var(--primary);">{{ $u->companies_count }} Companies</span>
            </td>
            <td class="muted" style="font-size:13px;">{{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}</td>
            <td>
              @if($u->status === 'suspended')
                <span style="background:#fee2e2; color:#dc2626; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:700;">SUSPENDED</span>
              @else
                <span style="background:#dcfce7; color:#16a34a; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:700;">ACTIVE</span>
              @endif
            </td>
            <td>
              <form action="{{ route('saas_admin.users.impersonate', $u) }}" method="POST" style="display:inline-block;">
                @csrf
                <button type="submit" class="btn btn-secondary" style="font-size:12px; padding:5px 10px;">
                  <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  <span>Troubleshoot</span>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="muted" style="text-align:center; padding:24px;">No customer accounts registered yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
