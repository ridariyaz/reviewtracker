@extends('layouts.app')

@section('title', 'Feedback · ReviewTracker')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Feedback inbox</div>
      <div class="page-subtitle">Internal comments and ratings from customers.</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a class="btn btn-secondary" href="{{ route('export.employees') }}">Export employees CSV</a>
      <a class="btn btn-secondary" href="{{ route('export.feedback') }}">Export feedback CSV</a>
    </div>
  </div>

  <div class="card">
    <div class="table-wrapper">
      <table>
        <tr>
          <th>Employee</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Status</th>
          <th>When</th>
          <th></th>
        </tr>
        @forelse($feedback as $item)
        <tr>
          <td>{{ $item->employee?->name ?? '—' }}</td>
          <td><span class="pill">{{ $item->rating }}</span></td>
          <td>{{ $item->comment ?: '—' }}</td>
          <td>{{ $item->status }}</td>
          <td class="muted">{{ $item->created_at }}</td>
          <td>
            <form action="{{ route('feedback.status', $item) }}" method="POST" style="display:flex;gap:6px;">
              @csrf
              <select name="status" class="input" style="width:auto;padding:6px 10px;">
                <option value="new" @selected($item->status === 'new')>new</option>
                <option value="in_progress" @selected($item->status === 'in_progress')>in_progress</option>
                <option value="resolved" @selected($item->status === 'resolved')>resolved</option>
              </select>
              <button class="btn" type="submit" style="padding:6px 10px;">Update</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="muted">No feedback yet.</td></tr>
        @endforelse
      </table>
    </div>
  </div>
@endsection
