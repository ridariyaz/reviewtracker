<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Tell us more · {{ $brandName }}</title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: radial-gradient(circle at top, {{ $brandSecondaryColor }}, #020617 55%);
      color: #e5e7eb;
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 16px; text-align: center;
    }
    .card {
      width: 100%; max-width: 500px;
      background: rgba(15, 23, 42, 0.95);
      border-radius: 24px; padding: 26px;
      box-shadow: 0 25px 60px rgba(0,0,0,0.65);
      border: 1px solid rgba(148,163,184,0.4);
    }
    .pill {
      display: inline-flex; gap: 6px; padding: 4px 10px; border-radius: 999px;
      border: 1px solid rgba(148,163,184,0.5); font-size: 11px;
      text-transform: uppercase; letter-spacing: 0.12em; color: #9ca3af; margin-bottom: 10px;
    }
    h1 { font-size: 24px; margin: 0 0 6px; }
    p { margin: 0 0 16px; font-size: 14px; color: #9ca3af; }
    textarea {
      width: 100%; min-height: 130px; padding: 10px 12px; border-radius: 12px;
      border: 1px solid #4b5563; background: #020617; color: #f9fafb; font-size: 14px; resize: vertical;
    }
    button {
      width: 100%; margin-top: 12px; padding: 14px; border-radius: 999px; border: none;
      background: linear-gradient(135deg, {{ $brandPrimaryColor }}, #2563eb);
      color: #f9fafb; font-size: 15px; font-weight: 500; cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="card">
    @if($brandLogoUrl)
    <div style="margin-bottom:18px;display:flex;justify-content:center;">
      <div style="width:96px;height:96px;border-radius:999px;overflow:hidden;border:2px solid rgba(148,163,184,0.6);background:#020617;">
        <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }} logo" style="width:100%;height:100%;object-fit:cover;">
      </div>
    </div>
    @endif
    <div class="pill">
      <span>Private feedback</span>
      <span>{{ strtoupper($rating) }}</span>
    </div>
    <h1>Tell us what we can improve</h1>
    <p>Your comments come straight to our team. They will not be posted publicly.</p>
    <form action="{{ route('review.submit') }}" method="POST">
      @csrf
      <input type="hidden" name="employee_id" value="{{ $employeeId }}">
      <input type="hidden" name="rating" value="{{ $rating }}">
      <textarea name="comment" placeholder="What worked, what didn’t, or anything specific we should know…"></textarea>
      <button type="submit">Send feedback privately</button>
    </form>
  </div>
</body>
</html>
