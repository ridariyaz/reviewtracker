@extends('layouts.app')

@section('title', 'Custom Code Injector & Announcements · Super Admin')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Custom Code Injector & Global Announcements</div>
    <div class="page-subtitle">Inject tracking scripts, Google Analytics, custom JS/CSS, or broadcast platform notices to customer dashboards.</div>
  </div>
  <a href="{{ route('saas_admin.index') }}" class="btn btn-secondary">&larr; SaaS Admin Dashboard</a>
</div>

<form action="{{ route('saas_admin.code.save') }}" method="POST">
  @csrf

  <!-- Global Announcement Banner -->
  <div class="card" style="padding:24px; margin-bottom:24px; border-top:4px solid #f59e0b;">
    <div style="font-size:1.1rem; font-weight:700; color:var(--text-heading); margin-bottom:4px;">📣 Platform Announcement Banner</div>
    <div class="muted" style="font-size:0.88rem; margin-bottom:14px;">Displays a high-visibility alert banner across all active customer dashboards. Leave empty to disable.</div>
    <textarea class="input" name="global_announcement" rows="2" placeholder="e.g. Scheduled system maintenance on Sunday at 2:00 AM UTC. Features will remain active.">{{ $globalAnnouncement }}</textarea>
  </div>

  <!-- Global Custom Script Injection -->
  <div class="card" style="padding:24px; margin-bottom:24px; border-top:4px solid #0284c7;">
    <div style="font-size:1.1rem; font-weight:700; color:var(--text-heading); margin-bottom:4px;">🌐 Global JavaScript / HTML / CSS Injector</div>
    <div class="muted" style="font-size:0.88rem; margin-bottom:14px;">Injected into the HTML header/footer of ALL customer review landing pages across the entire SaaS platform.</div>
    <textarea class="input" name="global_script" rows="5" style="font-family:monospace; font-size:13px; background:var(--bg-main);" placeholder="<script>
  console.log('SaaS Global Script Loaded');
</script>">{{ $globalScript }}</textarea>
  </div>

  <!-- Per-Company Specific Code Injection -->
  <div class="card" style="padding:24px; margin-bottom:24px; border-top:4px solid #6366f1;">
    <div style="font-size:1.1rem; font-weight:700; color:var(--text-heading); margin-bottom:4px;">🏢 Per-Company Specific Code Injection</div>
    <div class="muted" style="font-size:0.88rem; margin-bottom:14px;">Select a specific client company to inject custom tracking codes, pixel tags, or custom styling onto their review landing pages.</div>

    <div style="margin-bottom:16px;">
      <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">Select Company</label>
      <select class="input" id="companySelect" name="company_id" onchange="loadCompanyCustomCode(this)">
        <option value="">-- Choose a Company Profile --</option>
        @foreach($companies as $c)
          <option value="{{ $c->id }}" data-code="{{ e($c->custom_code ?? '') }}">{{ $c->name }} (Owner: {{ $c->user->username ?? 'Unknown' }})</option>
        @endforeach
      </select>
    </div>

    <div style="margin-bottom:16px;">
      <label style="display:block; font-size:13px; font-weight:700; margin-bottom:6px;">Custom Code for Selected Company</label>
      <textarea class="input" id="company_custom_code" name="company_custom_code" rows="5" style="font-family:monospace; font-size:13px; background:var(--bg-main);" placeholder="Select a company above to edit its custom code snippet..."></textarea>
    </div>
  </div>

  <div style="display:flex; justify-content:flex-end;">
    <button type="submit" class="btn" style="padding:12px 28px; font-size:1rem;">Save & Apply All Injections</button>
  </div>
</form>

<script>
  function loadCompanyCustomCode(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const code = selectedOption.getAttribute('data-code') || '';
    document.getElementById('company_custom_code').value = code;
  }
</script>
@endsection
