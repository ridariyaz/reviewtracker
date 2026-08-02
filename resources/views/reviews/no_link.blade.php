<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brandName }} - Notice</title>
    <style>
        :root {
            --primary: {{ $brandPrimaryColor }};
            --secondary: {{ $brandSecondaryColor }};
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--secondary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
            text-align: center;
        }
        .card {
            max-width: 440px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 36px 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }
        .icon-box {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(234, 179, 8, 0.15);
            border: 1px solid rgba(234, 179, 8, 0.3);
            color: #facc15;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-box svg {
            width: 32px;
            height: 32px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }
        h1 {
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: 12px;
        }
        p {
            opacity: 0.8;
            font-size: 0.95rem;
            line-height: 1.55;
            margin-bottom: 24px;
        }
        .note {
            font-size: 0.825rem;
            color: rgba(255, 255, 255, 0.5);
            background: rgba(0, 0, 0, 0.2);
            padding: 12px;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-box">
            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <h1>Review Link Not Set Yet</h1>
        <p>This business has not configured a public review link in their settings yet.</p>
        <div class="note">
            Please ask the store administrator to enter their main Google Review link in the settings panel.
        </div>
    </div>
</body>
</html>
