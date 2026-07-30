@extends('layouts.app')

@section('title', 'Dashboard · ReviewTracker')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Team & QR Dashboard</div>
      <div class="page-subtitle">
        Manage staff members, print review QR codes, and monitor Google review generation.
      </div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <button class="btn" onclick="openAddEmployeeModal()">+ Add Employee</button>
      <a href="{{ route('feedback.index') }}" class="btn btn-secondary" style="position:relative;">
        Feedback Inbox
        @if(($unresolvedFeedbackCount ?? 0) > 0)
          <span class="nav-badge" style="position:absolute;top:-6px;right:-6px;">{{ $unresolvedFeedbackCount }}</span>
        @endif
      </a>
    </div>
  </div>

  <div style="display:grid;gap:20px;" class="layout">
    <style>
      @media (min-width: 900px) {
        .layout { grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.2fr); }
      }
      .qr-thumb { border-radius:10px; border:1px solid rgba(148,163,184,0.4); padding:4px; background:#fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
      .badge-rank { font-size:12px; padding:3px 8px; border-radius:999px; border:1px solid #e5e7eb; background:#f9fafb; font-weight:700; }
      .badge-rank.top1 { border-color:#facc15; background:#fefce8; color:#854d0e; }
      .badge-rank.top2 { border-color:#a1a1aa; background:#f4f4f5; color:#3f3f46; }
      .badge-rank.top3 { border-color:#f97316; background:#fff7ed; color:#9a3412; }
      details.actions { position:relative; display:inline-block; }
      details.actions summary { list-style:none; cursor:pointer; padding:4px 10px; border-radius:999px; border:1px solid #cbd5e1; background:#f8fafc; font-size:14px; font-weight:600; }
      details.actions summary::-webkit-details-marker { display:none; }
      .actions-menu { position:absolute; right:0; margin-top:6px; padding:12px; border-radius:14px; background:#fff; box-shadow:0 10px 30px rgba(15,23,42,0.18); border:1px solid #e2e8f0; z-index:10; min-width:280px; }
      .action-inline-form { display:flex; flex-direction:column; gap:8px; margin-bottom:8px; }
      .action-input { width:100%; padding:8px 12px; font-size:13px; border-radius:999px; border:1px solid #cbd5e1; background:#f8fafc; }
      .btn-ghost { padding:6px 12px; font-size:12px; font-weight:600; border-radius:999px; border:1px solid transparent; background:#f1f5f9; color:#334155; cursor:pointer; text-align:center; }
      .btn-ghost:hover { border-color:#cbd5e1; background:#e2e8f0; }
      .btn-danger { color:#b91c1c; background:#fef2f2; }
      .btn-danger:hover { background:#fee2e2; }
      .small-link { font-size:12px; color:#2563eb; text-decoration:none; font-weight:600; }
    </style>

    <!-- Employees Directory Card -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Staff Directory</div>
          <div class="card-title">Active Team Members</div>
        </div>
        <div class="muted">{{ $employees->count() }} total</div>
      </div>

      <div class="table-wrapper">
        <table>
          <tr>
            <th style="width:32px;">#</th>
            <th>Employee</th>
            <th class="text-right">Scans</th>
            <th class="text-right">Good</th>
            <th class="text-right">OK</th>
            <th class="text-right">Bad</th>
            <th>QR Code</th>
            <th class="text-right">Manage</th>
          </tr>
          @foreach($employees as $employee)
          <tr>
            <td>
              <span class="badge-rank @if($loop->iteration === 1) top1 @elseif($loop->iteration === 2) top2 @elseif($loop->iteration === 3) top3 @endif">
                {{ $loop->iteration }}
              </span>
            </td>
            <td>
              <div style="font-weight:600; color:#0f172a;">{{ $employee->name }}</div>
              <div class="muted" style="font-size:11px;">@ {{ $employee->employee_username ?: 'no-login' }}</div>
            </td>
            <td class="text-right"><span class="pill">{{ $employee->scans }}</span></td>
            <td class="text-right"><span class="pill" style="background:#dcfce7;color:#15803d;">{{ $employee->good_count }}</span></td>
            <td class="text-right"><span class="pill" style="background:#fefce8;color:#a16207;">{{ $employee->ok_count }}</span></td>
            <td class="text-right"><span class="pill" style="background:#fef2f2;color:#b91c1c;">{{ $employee->bad_count }}</span></td>
            <td>
              <img class="qr-thumb" src="{{ asset('storage/qrcodes/'.$employee->id.'.png') }}" width="64" alt="QR for {{ $employee->name }}">
              <div><a class="small-link" href="{{ route('review.show', $employee) }}" target="_blank">Test Customer Funnel ↗</a></div>
            </td>
            <td class="text-right">
              <details class="actions">
                <summary>Actions ▾</summary>
                <div class="actions-menu">
                  <div style="font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">Edit {{ $employee->name }}</div>
                  
                  <form class="action-inline-form" action="{{ route('employees.update', $employee) }}" method="POST">
                    @csrf
                    <input class="action-input" type="text" name="name" value="{{ $employee->name }}" placeholder="Full Name">
                    <button class="btn-ghost" type="submit">Update Name</button>
                  </form>

                  <form class="action-inline-form" action="{{ route('employees.credentials', $employee) }}" method="POST" style="margin-top:8px;">
                    @csrf
                    <input class="action-input" type="text" name="employee_username" value="{{ $employee->employee_username }}" placeholder="Login Username">
                    <input class="action-input" type="password" name="employee_password" placeholder="New Password (min 8)">
                    <button class="btn-ghost" type="submit">Update Login Password</button>
                  </form>

                  <form style="margin-top:10px;" action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Remove {{ $employee->name }} and their feedback?');">
                    @csrf
                    <button class="btn-ghost btn-danger" style="width:100%;" type="submit">Delete Employee</button>
                  </form>
                </div>
              </details>
            </td>
          </tr>
          @endforeach
        </table>
      </div>

      @if($employees->isEmpty())
        <div style="text-align:center; padding:30px 10px;">
          <p class="muted">No employees added yet.</p>
          <button class="btn" onclick="openAddEmployeeModal()">+ Add Your First Employee</button>
        </div>
      @endif
    </div>

    <!-- Leaderboard Sidebar -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Leaderboard</div>
          <div class="card-title">Top Staff Performers</div>
        </div>
      </div>
      @if($employees->count())
        <ol style="margin:0;padding-left:18px;">
          @foreach($employees as $employee)
          <li style="margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <div>
                <div style="font-weight:600; color:#0f172a;">{{ $employee->name }}</div>
                <div class="muted" style="font-size:11px;">ID #{{ $employee->id }}</div>
              </div>
              <div style="text-align:right;">
                <div class="pill" style="margin-bottom:2px;">{{ $employee->scans }} scans</div>
                <div class="muted" style="font-size:11px;">👍 {{ $employee->good_count }} · 😐 {{ $employee->ok_count }} · ⚠️ {{ $employee->bad_count }}</div>
              </div>
            </div>
          </li>
          @endforeach
        </ol>
      @else
        <p class="muted">Add your team members to track performance.</p>
      @endif
    </div>
  </div>

  <!-- Add Employee Modal Popup -->
  <div id="addEmployeeModal" class="modal-backdrop">
    <div class="modal-box">
      <div class="modal-header">
        <h3 class="modal-title">Add Team Member</h3>
        <button class="modal-close" onclick="closeAddEmployeeModal()">✕</button>
      </div>
      <p class="muted" style="margin-bottom:16px;">
        Add employee details to generate their unique review QR code and staff portal credentials.
      </p>
      <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        <div class="field">
          <label for="emp_name">Full Name <span style="color:#ef4444;">*</span></label>
          <input class="input" type="text" id="emp_name" name="name" placeholder="e.g. Sarah Connor" required>
        </div>
        <div class="field">
          <label for="emp_username">Staff Login Username (Optional)</label>
          <input class="input" type="text" id="emp_username" name="employee_username" placeholder="e.g. sarah">
        </div>
        <div class="field">
          <label for="emp_password">Staff Login Password (Optional, min 8 chars)</label>
          <input class="input" type="password" id="emp_password" name="employee_password" placeholder="••••••••">
        </div>
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAddEmployeeModal()">Cancel</button>
          <button type="submit" class="btn">Create Employee & QR</button>
        </div>
      </form>
    </div>
  </div>

  <!-- First-Time Welcome Modal Popup -->
  <div id="welcomeModal" class="modal-backdrop">
    <div class="modal-box" style="max-width:520px;">
      <div class="modal-header">
        <h3 class="modal-title">⚡ Welcome to ReviewTracker!</h3>
        <button class="modal-close" onclick="dismissWelcomeModal()">✕</button>
      </div>
      <div style="font-size:14px; color:#475569; line-height:1.5; margin-bottom:20px;">
        ReviewTracker makes capturing 5-star Google Reviews effortless for your business in 3 quick steps:
        <ol style="margin-top:10px; padding-left:20px; color:#1e293b;">
          <li style="margin-bottom:6px;"><strong>Add Team Members:</strong> Click <em>"+ Add Employee"</em> to generate unique staff QR codes.</li>
          <li style="margin-bottom:6px;"><strong>Present to Customers:</strong> Staff present their QR code to happy customers at checkout or tables.</li>
          <li style="margin-bottom:6px;"><strong>Smart Funnel Routing:</strong> 5-Star reviews open your Google page; neutral/bad ratings stay in your private inbox!</li>
        </ol>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <a href="{{ route('help') }}" style="font-size:13px; font-weight:600; color:#2563eb; text-decoration:none;">Read Full Explainer Guide →</a>
        <button class="btn" onclick="dismissWelcomeModal()">Got it, let's start!</button>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  function openAddEmployeeModal() {
    document.getElementById('addEmployeeModal').classList.add('active');
  }

  function closeAddEmployeeModal() {
    document.getElementById('addEmployeeModal').classList.remove('active');
  }

  function dismissWelcomeModal() {
    localStorage.setItem('reviewtracker_welcome_dismissed', '1');
    document.getElementById('welcomeModal').classList.remove('active');
  }

  // Check if first time opening
  document.addEventListener('DOMContentLoaded', function () {
    if (!localStorage.getItem('reviewtracker_welcome_dismissed')) {
      setTimeout(function () {
        document.getElementById('welcomeModal').classList.add('active');
      }, 400);
    }
  });
</script>
@endsection
