@extends('layouts.app')

@section('title', 'Dashboard · ReviewTracker')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Dashboard</div>
      <div class="page-subtitle">
        Add employees, generate QR codes, preview the customer journey and track who is driving the most reviews.
      </div>
    </div>
    <div>
      <a href="{{ route('feedback.index') }}" class="btn btn-secondary">Open feedback inbox</a>
    </div>
  </div>

  <div style="display:grid;gap:20px;" class="layout">
    <style>
      @media (min-width: 900px) {
        .layout { grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.3fr); }
      }
      .field-row { display:flex; flex-wrap:wrap; gap:10px; margin-top:10px; }
      .field-row .input { flex: 1 1 200px; }
      .qr-thumb { border-radius:8px; border:1px solid rgba(148,163,184,0.4); padding:4px; background:#fff; }
      .badge-rank { font-size:12px; padding:3px 8px; border-radius:999px; border:1px solid #e5e7eb; background:#f9fafb; }
      .badge-rank.top1 { border-color:#facc15; background:#fefce8; }
      .badge-rank.top2 { border-color:#a1a1aa; background:#f4f4f5; }
      .badge-rank.top3 { border-color:#f97316; background:#fff7ed; }
      details.actions { position:relative; display:inline-block; }
      details.actions summary { list-style:none; cursor:pointer; padding:4px 8px; border-radius:999px; border:1px solid #e5e7eb; background:#f9fafb; font-size:16px; }
      details.actions summary::-webkit-details-marker { display:none; }
      .actions-menu { position:absolute; right:0; margin-top:6px; padding:8px; border-radius:10px; background:#fff; box-shadow:0 10px 30px rgba(15,23,42,0.18); border:1px solid #e5e7eb; z-index:10; min-width:260px; }
      .action-inline-form { display:flex; gap:6px; align-items:center; margin-bottom:4px; flex-wrap:wrap; }
      .action-input { width:140px; padding:6px 8px; font-size:13px; border-radius:999px; border:1px solid #d4d4d8; background:#f9fafb; }
      .btn-ghost { padding:4px 10px; font-size:12px; border-radius:999px; border:1px solid transparent; background:transparent; color:#4b5563; cursor:pointer; }
      .btn-ghost:hover { border-color:#d4d4d8; background:#f3f4f6; }
      .btn-danger { color:#b91c1c; }
      .small-link { font-size:12px; color:#2563eb; text-decoration:none; }
    </style>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Employees</div>
          <div class="card-title">Add team member</div>
        </div>
      </div>

      <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        <div class="field-row">
          <input class="input" type="text" name="name" placeholder="Employee name" required>
          <button class="btn" type="submit">Create QR code</button>
        </div>
        <p class="muted" style="margin-top: 8px;">
          Each employee gets a unique QR that routes customers into the feedback flow.
        </p>
      </form>

      <div style="margin-top: 20px;" class="card-header">
        <div>
          <div class="card-kicker">Directory</div>
          <div class="card-title">Active employees</div>
        </div>
        <div class="muted">{{ $employees->count() }} total</div>
      </div>

      <div class="table-wrapper">
        <table>
          <tr>
            <th style="width:32px;">#</th>
            <th>Employee</th>
            <th class="text-right">Total scans</th>
            <th class="text-right">Good</th>
            <th class="text-right">OK</th>
            <th class="text-right">Bad</th>
            <th>QR code</th>
            <th class="text-right">Actions</th>
          </tr>
          @foreach($employees as $employee)
          <tr>
            <td>
              <span class="badge-rank @if($loop->iteration === 1) top1 @elseif($loop->iteration === 2) top2 @elseif($loop->iteration === 3) top3 @endif">
                {{ $loop->iteration }}
              </span>
            </td>
            <td>
              <div>{{ $employee->name }}</div>
              <div class="muted">ID #{{ $employee->id }}</div>
            </td>
            <td class="text-right"><span class="pill">{{ $employee->scans }} <span style="font-size:11px;text-transform:uppercase;">total</span></span></td>
            <td class="text-right"><span class="pill">{{ $employee->good_count }}</span></td>
            <td class="text-right"><span class="pill">{{ $employee->ok_count }}</span></td>
            <td class="text-right"><span class="pill">{{ $employee->bad_count }}</span></td>
            <td>
              <img class="qr-thumb" src="{{ asset('storage/qrcodes/'.$employee->id.'.png') }}" width="72" alt="QR for {{ $employee->name }}">
              <div><a class="small-link" href="{{ route('review.show', $employee) }}" target="_blank">Preview customer screen</a></div>
            </td>
            <td class="text-right">
              <details class="actions">
                <summary>⋯</summary>
                <div class="actions-menu">
                  <form class="action-inline-form" action="{{ route('employees.update', $employee) }}" method="POST">
                    @csrf
                    <input class="action-input" type="text" name="name" placeholder="Rename…">
                    <button class="btn-ghost" type="submit">Save</button>
                  </form>
                  <form class="action-inline-form" action="{{ route('employees.credentials', $employee) }}" method="POST" style="margin-top:6px;">
                    @csrf
                    <input class="action-input" type="text" name="employee_username" placeholder="Login username…">
                    <input class="action-input" type="password" name="employee_password" placeholder="Login password…">
                    <button class="btn-ghost" type="submit">Set login</button>
                  </form>
                  <form style="display:block;margin-top:6px;" action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Remove this employee and their feedback?');">
                    @csrf
                    <button class="btn-ghost btn-danger" type="submit">Delete</button>
                  </form>
                </div>
              </details>
            </td>
          </tr>
          @endforeach
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Leaderboard</div>
          <div class="card-title">Top performers</div>
        </div>
      </div>
      @if($employees->count())
        <ol style="margin:0;padding-left:18px;">
          @foreach($employees as $employee)
          <li style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
              <div>
                <div style="font-weight:500;">{{ $employee->name }}</div>
                <div class="muted">ID #{{ $employee->id }}</div>
              </div>
              <div>
                <div class="pill" style="margin-bottom:4px;">{{ $employee->scans }} total</div>
                <div class="muted" style="font-size:12px;">👍 {{ $employee->good_count }} · 😐 {{ $employee->ok_count }} · ⚠️ {{ $employee->bad_count }}</div>
              </div>
            </div>
          </li>
          @endforeach
        </ol>
      @else
        <p class="muted">Add your first employee to start tracking performance.</p>
      @endif
    </div>
  </div>
@endsection
