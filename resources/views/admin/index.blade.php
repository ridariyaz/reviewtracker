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
      .qr-thumb-box {
        border-radius: 10px;
        border: 1px solid var(--border-color);
        padding: 4px;
        background: #ffffff;
        display: inline-block;
      }
      .qr-thumb-box img { display: block; }
      .badge-rank { font-size:12px; padding:3px 8px; border-radius:999px; border:1px solid var(--border-color); background:var(--input-bg); font-weight:700; color:var(--text-main); }
      .badge-rank.top1 { border-color:#facc15; background:#fefce8; color:#854d0e; }
      .badge-rank.top2 { border-color:#a1a1aa; background:#f4f4f5; color:#3f3f46; }
      .badge-rank.top3 { border-color:#f97316; background:#fff7ed; color:#9a3412; }
      .btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 8px; font-weight: 600; text-decoration: none; border: 1px solid transparent; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
      .btn-sm-edit { background: var(--input-bg); color: var(--text-main); border-color: var(--border-color); }
      .btn-sm-edit:hover { border-color: var(--primary); color: var(--primary); }
      .btn-sm-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.2); }
      .btn-sm-danger:hover { background: rgba(239, 68, 68, 0.2); }
      .small-link { font-size:12px; color:var(--primary); text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:2px; margin-top:4px; }
      .small-link:hover { text-decoration:underline; }
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
          <thead>
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
          </thead>
          <tbody>
            @foreach($employees as $employee)
            <tr>
              <td>
                <span class="badge-rank @if($loop->iteration === 1) top1 @elseif($loop->iteration === 2) top2 @elseif($loop->iteration === 3) top3 @endif">
                  {{ $loop->iteration }}
                </span>
              </td>
              <td>
                <div style="font-weight:700; color:var(--text-heading);">{{ $employee->name }}</div>
                <div class="muted" style="font-size:11px;">@ {{ $employee->employee_username ?: 'no-login' }}</div>
              </td>
              <td class="text-right"><span class="pill">{{ $employee->scans }}</span></td>
              <td class="text-right"><span class="pill" style="background:rgba(34,197,94,0.15);color:#22c55e;">{{ $employee->good_count }}</span></td>
              <td class="text-right"><span class="pill" style="background:rgba(234,179,8,0.15);color:#eab308;">{{ $employee->ok_count }}</span></td>
              <td class="text-right"><span class="pill" style="background:rgba(239,68,68,0.15);color:#ef4444;">{{ $employee->bad_count }}</span></td>
              <td>
                @php
                  $qrUrl = route('review.show', ['employee' => $employee->id]);
                  $svgDataUri = (new \App\Services\QrCodeService())->generateSvgDataUri($qrUrl);
                @endphp
                <div class="qr-thumb-box">
                  <img src="{{ $svgDataUri }}" width="56" height="56" alt="QR for {{ $employee->name }}">
                </div>
                <div><a class="small-link" href="{{ route('review.show', $employee) }}" target="_blank">Test QR ↗</a></div>
              </td>
              <td class="text-right">
                <div style="display:flex; gap:6px; justify-content:flex-end;">
                  <button class="btn-sm btn-sm-edit" onclick="openEditEmployeeModal('{{ $employee->id }}', '{{ $employee->name }}', '{{ $employee->employee_username }}')" title="Edit {{ $employee->name }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  </button>
                  <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Remove {{ $employee->name }}?');" style="margin:0;">
                    @csrf
                    <button class="btn-sm btn-sm-danger" type="submit" title="Delete {{ $employee->name }}">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
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
          <li style="margin-bottom:14px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <div>
                <div style="font-weight:700; color:var(--text-heading);">{{ $employee->name }}</div>
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
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px;" for="emp_name">Full Name <span style="color:#ef4444;">*</span></label>
          <input class="input" type="text" id="emp_name" name="name" placeholder="e.g. Sarah Connor" required>
        </div>
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px;" for="emp_username">Staff Login Username (Optional)</label>
          <input class="input" type="text" id="emp_username" name="employee_username" placeholder="e.g. sarah">
        </div>
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px;" for="emp_password">Staff Login Password (Optional, min 8 chars)</label>
          <input class="input" type="password" id="emp_password" name="employee_password" placeholder="••••••••">
        </div>
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
          <button type="button" class="btn btn-secondary" onclick="closeAddEmployeeModal()">Cancel</button>
          <button type="submit" class="btn">Create Employee & QR</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Employee Modal Popup -->
  <div id="editEmployeeModal" class="modal-backdrop">
    <div class="modal-box">
      <div class="modal-header">
        <h3 class="modal-title">Edit Staff Credentials</h3>
        <button class="modal-close" onclick="closeEditEmployeeModal()">✕</button>
      </div>
      <form id="editEmployeeForm" method="POST">
        @csrf
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px;" for="edit_emp_name">Full Name <span style="color:#ef4444;">*</span></label>
          <input class="input" type="text" id="edit_emp_name" name="name" required>
        </div>
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px;" for="edit_emp_username">Login Username</label>
          <input class="input" type="text" id="edit_emp_username" name="employee_username">
        </div>
        <div style="margin-bottom:14px;">
          <label style="display:block;font-size:13px;font-weight:600;margin-bottom:4px;" for="edit_emp_password">New Login Password (min 8 chars)</label>
          <input class="input" type="password" id="edit_emp_password" name="employee_password" placeholder="Leave blank to keep current password">
        </div>
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
          <button type="button" class="btn btn-secondary" onclick="closeEditEmployeeModal()">Cancel</button>
          <button type="submit" class="btn">Save Changes</button>
        </div>
      </form>
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

  function openEditEmployeeModal(id, name, username) {
    document.getElementById('edit_emp_name').value = name;
    document.getElementById('edit_emp_username').value = username;
    document.getElementById('editEmployeeForm').action = '/edit_employee/' + id;
    document.getElementById('editEmployeeModal').classList.add('active');
  }

  function closeEditEmployeeModal() {
    document.getElementById('editEmployeeModal').classList.remove('active');
  }
</script>
@endsection
