<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Tell us more · {{ $brandName }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: radial-gradient(ellipse at top, {{ $brandSecondaryColor }}, #020617 80%);
      color: #e5e7eb;
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 20px; text-align: center;
      -webkit-font-smoothing: antialiased;
    }
    .card {
      width: 100%; max-width: 480px;
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(16px);
      border-radius: 24px; padding: 32px 28px;
      box-shadow: 0 25px 60px rgba(0,0,0,0.65);
      border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .brand-logo {
      width: 80px; height: 80px; border-radius: 999px;
      overflow: hidden; border: 3px solid rgba(255,255,255,0.2);
      background: #020617; margin: 0 auto 16px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }
    .brand-logo img { width: 100%; height: 100%; object-fit: cover; }
    .pill {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 5px 14px; border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      font-size: 12px; font-weight: 600; letter-spacing: 0.05em;
      color: #cbd5e1; margin-bottom: 14px; text-transform: uppercase;
    }
    h1 { font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 8px; color: #ffffff; }
    p { margin: 0 0 20px; font-size: 14px; color: #94a3b8; line-height: 1.5; }
    
    textarea {
      width: 100%; min-height: 130px; padding: 14px; border-radius: 16px;
      border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(2, 6, 23, 0.8);
      color: #f8fafc; font-family: inherit; font-size: 14px; resize: vertical;
      transition: all 0.2s ease; outline: none;
    }
    textarea:focus { border-color: {{ $brandPrimaryColor }}; box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25); background: #020617; }
    
    button {
      width: 100%; margin-top: 16px; padding: 15px; border-radius: 999px; border: none;
      background: linear-gradient(135deg, {{ $brandPrimaryColor }}, #2563eb);
      color: #ffffff; font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700;
      cursor: pointer; transition: all 0.2s ease;
      box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
    }
    button:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(37, 99, 235, 0.5); }
  </style>
</head>
<body>
  <div class="card">
    @if($brandLogoUrl)
    <div class="brand-logo">
      <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }} logo">
    </div>
    @endif
    <h1>Tell us how we can improve</h1>
    <p>Your notes go directly to management to help us improve our service.</p>
    <form action="{{ route('review.submit') }}" method="POST">
      @csrf
      <input type="hidden" name="employee_id" value="{{ $employeeId }}">
      <input type="hidden" name="rating" value="{{ $rating }}">
      <textarea name="comment" placeholder="What worked well, what didn't, or anything specific we should fix..."></textarea>
      <button type="submit">Submit Feedback</button>
    </form>
  </div>
</body>
</html>
