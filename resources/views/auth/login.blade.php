@extends('layouts.auth')

@section('title', 'Admin Login · ReviewTracker')

@section('content')
  <div class="brand">
    <div class="brand-badge">R</div>
    <div>
      <div class="brand-title">ReviewTracker</div>
      <div class="brand-subtitle">Private admin access</div>
    </div>
  </div>

  <h1>Sign in</h1>
  <p class="subtitle">Log in to manage employees, QR codes and internal feedback.</p>

  <form method="POST" action="{{ url('/login') }}">
    @csrf
    <div class="field">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" value="{{ old('username') }}" required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
    </div>

    @if(session('error'))
      <div class="error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif

    <button type="submit">Continue to dashboard</button>

    <div class="hint">
      New here?
      <a href="{{ route('signup') }}">Create an admin account</a><br>
      <span style="display:block;margin-top:6px;">
        Employee?
        <a href="{{ route('employee.login') }}">Log in to see your QR and stats</a>
      </span>
    </div>
  </form>
@endsection
