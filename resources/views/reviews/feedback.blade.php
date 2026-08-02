<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brandName }} - How was your experience?</title>
    <style>
        :root {
            --primary: {{ $brandPrimaryColor }};
            --secondary: {{ $brandSecondaryColor }};
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--secondary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
        }
        .card {
            max-width: 440px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }
        .logo {
            max-height: 64px;
            margin-bottom: 20px;
            object-fit: contain;
        }
        .avatar-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        .stars {
            display: flex;
            justify-content: center;
            gap: 6px;
            font-size: 1.2rem;
            color: #f59e0b;
            margin-bottom: 12px;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        p.tagline {
            opacity: 0.8;
            font-size: 0.95rem;
            margin-bottom: 28px;
            line-height: 1.5;
        }
        .buttons {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.05rem;
            color: #fff;
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .btn:active {
            transform: scale(0.98);
        }
        .btn-good {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }
        .btn-good:hover {
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
        }
        .btn-ok {
            background: linear-gradient(135deg, #eab308, #ca8a04);
        }
        .btn-ok:hover {
            box-shadow: 0 6px 20px rgba(234, 179, 8, 0.4);
        }
        .btn-bad {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        .btn-bad:hover {
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        .trust-badge {
            margin-top: 24px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    </style>
</head>
<body>
    <div class="card">
        @if($brandLogoUrl)
            <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="logo">
        @endif

        <div class="avatar-circle">
            {{ strtoupper(substr($employee->name, 0, 1)) }}
        </div>

        <div class="stars">
            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
        </div>

        <h1>How was your experience with {{ $employee->name }}?</h1>
        <p class="tagline">Your honest feedback helps {{ $brandName }} deliver the best service possible.</p>

        <div class="buttons">
            <a href="{{ route('review.good', $employee) }}" class="btn btn-good">
                <span>🤩</span> Great Experience
            </a>
            <a href="{{ route('review.ok', $employee) }}" class="btn btn-ok">
                <span>😐</span> Okay / Average
            </a>
            <a href="{{ route('review.bad', $employee) }}" class="btn btn-bad">
                <span>😕</span> Needs Improvement
            </a>
        </div>

        <div class="trust-badge">
            <span>🔒</span> Direct & Verified Customer Feedback for {{ $brandName }}
        </div>
    </div>
</body>
</html>
