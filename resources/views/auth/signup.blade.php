@extends('layouts.auth')

@section('title', 'Sign up · ReviewTracker')

@section('content')
  <div class="brand">
    <div class="brand-badge">R</div>
    <div>
      <div class="brand-title">ReviewTracker</div>
      <div class="brand-subtitle">Create your admin account</div>
    </div>
  </div>

  <h1>Sign up</h1>
  <p class="subtitle">Choose a username and password to access the dashboard.</p>

  <form method="POST" action="{{ route('signup') }}">
    @csrf
    <div class="field">
      <label for="email">Email (optional but recommended)</label>
      <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com">
    </div>
    <div class="field">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" value="{{ old('username') }}" required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
    </div>

    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif

    <button type="submit">Create account</button>

    <div class="hint">
      Already have an account?
      <a href="{{ route('login') }}">Log in</a>
    </div>
  </form>
@endsection
