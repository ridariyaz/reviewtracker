@extends('layouts.app')

@section('title', 'Customer Accounts Management · Super Admin')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Customer Accounts & Troubleshooting</div>
    <div class="page-subtitle">Inspect customer accounts, launch 1-click troubleshooting mode, or manage access permissions.</div>
  </div>
  <a href="{{ route('saas_admin.index') }}" class="btn btn-secondary">&larr; SaaS Admin Dashboard</a>
</div>

<!-- Search Filter Card -->
<div class="card" style="padding:16px 20px; margin-bottom:20px;">
  <form method="GET" action="{{ route('saas_admin.users') }}" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <input class="input" type="text" name="search" value="{{ request('search') }}" placeholder="Search username or email..." style="max-width:320px;">
    <button type="submit" class="btn">Filter Accounts</button>
    @if(request('search'))
      <a href="{{ route('saas_admin.users') }}" class="btn btn-secondary">Clear Filter</a>
    @endif
  </form>
</div>

<!-- Users Table -->
<div class="card" style="padding:24px;">
  <div style="overflow-x:auto;">
    <table class="table" style="width:100%;">
      <thead>
        <tr>
          <th>User ID</th>
          <th>Username / Email</th>
          <th>Companies</th>
          <th>Account Status</th>
          <th>Super Admin</th>
          <th>Troubleshoot & Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          <tr>
            <td>#{{ $u->id }}</td>
            <td>
              <div style="font-weight:700; color:var(--text-heading);">{{ $u->username }}</div>
              <div class="muted" style="font-size:12px;">{{ $u->email ?: 'No email registered' }}</div>
            </td>
            <td>
              <span class="badge" style="background:var(--primary-subtle); color:var(--primary);">{{ $u->companies_count }} Companies</span>
            </td>
            <td>
              @if($u->status === 'suspended')
                <span style="background:#fee2e2; color:#dc2626; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:700;">SUSPENDED</span>
              @else
                <span style="background:#dcfce7; color:#16a34a; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:700;">ACTIVE</span>
              @endif
            </td>
            <td>
              @if($u->is_superadmin)
                <span style="background:#e0e7ff; color:#4338ca; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:700;">SUPER ADMIN</span>
              @else
                <span class="muted" style="font-size:12px;">Standard Client</span>
              @endif
            </td>
            <td>
              <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                <!-- Impersonate Troubleshoot -->
                @if($u->id !== Auth::id())
                  <form action="{{ route('saas_admin.users.impersonate', $u) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="font-size:11px; padding:4px 8px;" title="Log into user account to debug UI/settings">
                      <span>Troubleshoot</span>
                    </button>
                  </form>

                  <!-- Toggle Suspend / Active -->
                  <form action="{{ route('saas_admin.users.status', $u) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="font-size:11px; padding:4px 8px; color: {{ $u->status === 'suspended' ? '#16a34a' : '#dc2626' }};">
                      {{ $u->status === 'suspended' ? 'Activate' : 'Suspend' }}
                    </button>
                  </form>

                  <!-- Toggle Superadmin -->
                  <form action="{{ route('saas_admin.users.superadmin', $u) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="font-size:11px; padding:4px 8px;">
                      {{ $u->is_superadmin ? 'Revoke Super' : 'Make Super' }}
                    </button>
                  </form>

                  <!-- Delete Account -->
                  <form action="{{ route('saas_admin.users.delete', $u) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete account {{ $u->username }} and all associated company data? This action cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="font-size:11px; padding:4px 8px; color:#dc2626;">
                      Delete
                    </button>
                  </form>
                @else
                  <span class="muted" style="font-size:12px; font-style:italic;">Your Current Account</span>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted" style="text-align:center; padding:24px;">No customer accounts found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="margin-top:20px;">
    {{ $users->links() }}
  </div>
</div>
@endsection
