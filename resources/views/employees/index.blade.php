@extends('layouts.app')

@section('title', 'Employees · ReviewTracker')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Employees</div>
      <div class="page-subtitle">Directory for the current company.</div>
    </div>
    <a href="{{ route('admin') }}" class="btn">Back to dashboard</a>
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
        </tr>
        @foreach($employees as $employee)
        <tr>
          <td>{{ $employee->name }}</td>
          <td>{{ $employee->employee_username ?: '—' }}</td>
          <td class="text-right">{{ $employee->scans }}</td>
          <td class="text-right">{{ $employee->good_count }}</td>
          <td class="text-right">{{ $employee->ok_count }}</td>
          <td class="text-right">{{ $employee->bad_count }}</td>
        </tr>
        @endforeach
      </table>
    </div>
  </div>
@endsection
