@extends('layouts.app')

@section('title', 'Employees Directory · ReviewTracker')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Employees Directory</div>
      <div class="page-subtitle">View staff member performance, credentials, and QR codes for {{ $brandName }}.</div>
    </div>
    <div style="display:flex;gap:10px;">
      <button class="btn" onclick="openAddEmployeeModal()">+ Add Employee</button>
      <a href="{{ route('admin') }}" class="btn btn-secondary">Dashboard</a>
    </div>
  </div>

  <div class="card">
    <div class="table-wrapper">
      <table>
        <tr>
          <th>Name</th>
          <th>Username</th>
          <th class="text-right">Scans</th>
          <th class="text-right">Good</th>
          <th class="text-right">OK</th>
          <th class="text-right">Bad</th>
          <th>QR Code</th>
        </tr>
        @foreach($employees as $employee)
        <tr>
          <td><strong>{{ $employee->name }}</strong></td>
          <td><code>{{ $employee->employee_username ?: '—' }}</code></td>
          <td class="text-right"><span class="pill">{{ $employee->scans }}</span></td>
          <td class="text-right"><span class="pill" style="background:#dcfce7;color:#15803d;">{{ $employee->good_count }}</span></td>
          <td class="text-right"><span class="pill" style="background:#fefce8;color:#a16207;">{{ $employee->ok_count }}</span></td>
          <td class="text-right"><span class="pill" style="background:#fef2f2;color:#b91c1c;">{{ $employee->bad_count }}</span></td>
          <td>
            <a class="btn btn-secondary" style="font-size:12px;padding:4px 10px;" href="{{ asset('storage/qrcodes/'.$employee->id.'.png') }}" download="employee_{{ $employee->id }}_qr.png">
              Download QR ⬇
            </a>
          </td>
        </tr>
        @endforeach
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
