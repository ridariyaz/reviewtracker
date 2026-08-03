@extends('layouts.app')

@section('title', 'Employees Directory · ReviewTracker')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Employees Directory</div>
      <div class="page-subtitle">View staff member performance, credentials, and printable QR codes for {{ $brandName }}.</div>
    </div>
    <div style="display:flex;gap:10px;">
      <button class="btn" onclick="openAddEmployeeModal()">
        <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        <span>Add Employee</span>
      </button>
      <a href="{{ route('admin') }}" class="btn btn-secondary">Dashboard</a>
    </div>
  </div>

  <div class="card">
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Username</th>
            <th class="text-right">Scans</th>
            <th class="text-right">Good</th>
            <th class="text-right">OK</th>
            <th class="text-right">Bad</th>
            <th>Actions & QR</th>
          </tr>
        </thead>
        <tbody>
          @foreach($employees as $employee)
          @php
            $qrUrl = route('review.show', ['employee' => $employee->id]);
            $svgDataUri = (new \App\Services\QrCodeService())->generateSvgDataUri($qrUrl);
          @endphp
          <tr>
            <td>
              <div style="font-weight:700; color:var(--text-heading);">{{ $employee->name }}</div>
            </td>
            <td><code>{{ $employee->employee_username ?: '—' }}</code></td>
            <td class="text-right"><span class="pill">{{ $employee->scans }}</span></td>
            <td class="text-right"><span class="pill" style="background:rgba(34,197,94,0.15);color:#22c55e;">{{ $employee->good_count }}</span></td>
            <td class="text-right"><span class="pill" style="background:rgba(234,179,8,0.15);color:#eab308;">{{ $employee->ok_count }}</span></td>
            <td class="text-right"><span class="pill" style="background:rgba(239,68,68,0.15);color:#ef4444;">{{ $employee->bad_count }}</span></td>
            <td>
              <div style="display:flex; gap:8px; align-items:center;">
                <a class="btn btn-secondary" style="font-size:12px;padding:6px 12px;display:inline-flex;align-items:center;gap:6px;" href="{{ $svgDataUri }}" download="qr-code-{{ \Illuminate\Support\Str::slug($employee->name) }}.svg">
                  <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                  <span>Download QR</span>
                </a>
                <a href="{{ route('employee.qr', $employee) }}" target="_blank" class="btn" style="font-size:12px;padding:6px 12px;">
                  <span>Print Standee ↗</span>
                </a>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($employees->isEmpty())
      <div style="text-align:center; padding:30px 10px;">
        <p class="muted">No employees found.</p>
        <button class="btn" onclick="openAddEmployeeModal()">+ Add First Employee</button>
      </div>
    @endif
  </div>

  <!-- Add Employee Modal Popup -->
  <div id="addEmployeeModal" class="modal-backdrop">
    <div class="modal-box">
      <div class="modal-header">
        <h3 class="modal-title">Add Team Member</h3>
        <button class="modal-close" onclick="closeAddEmployeeModal()">✕</button>
      </div>
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
@endsection

@section('scripts')
<script>
  function openAddEmployeeModal() {
    document.getElementById('addEmployeeModal').classList.add('active');
  }

  function closeAddEmployeeModal() {
    document.getElementById('addEmployeeModal').classList.remove('active');
  }
</script>
@endsection
