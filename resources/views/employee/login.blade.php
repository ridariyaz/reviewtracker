@extends('layouts.auth')

@section('title', 'Employee login · ReviewTracker')

@section('content')
  <h1>Employee login</h1>
  <p class="subtitle">Access your QR code, stats and feedback.</p>

  <form method="POST" action="{{ url('/employee/login') }}">
    @csrf
    <div class="field">
      <label for="username">Employee username</label>
      <input id="username" name="username" type="text" value="{{ old('username') }}" required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
    </div>

    @if(session('error'))
      <div class="error">{{ session('error') }}</div>
    @endif

    <button type="submit">Continue</button>

    <div class="hint">
      Admin?
      <a href="{{ route('login') }}">Go to admin login</a>
    </div>
  </form>
@endsection
