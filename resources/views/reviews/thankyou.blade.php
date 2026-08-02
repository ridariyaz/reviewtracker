<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #020617;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
            text-align: center;
        }
        .card {
            max-width: 420px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 36px 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }
        .check-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(34, 197, 94, 0.15);
            border: 2px solid rgba(34, 197, 94, 0.4);
            color: #4ade80;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 20px;
            animation: pulse 2s infinite ease-in-out;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        p {
            opacity: 0.8;
            font-size: 0.98rem;
            line-height: 1.55;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="check-circle">✓</div>
        <h1>Thank you 🙏</h1>
        <p>Your feedback has been received privately and sent directly to management. We appreciate your time and help in making our service better!</p>
    </div>
</body>
</html>
