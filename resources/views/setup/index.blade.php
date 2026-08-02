<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome & Setup · ReviewTracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .setup-card {
            max-width: 540px;
            width: 100%;
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }
        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 16px;
        }
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: #0f172a;
        }
        p.subtitle {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.55;
            margin-bottom: 28px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        .input-field {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            font-size: 0.95rem;
            color: #0f172a;
            transition: all 0.2s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .helper {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 6px;
            line-height: 1.4;
        }
        .btn-submit {
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            border: none;
            background: #2563eb;
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
            transition: background 0.2s ease;
        }
        .btn-submit:hover {
            background: #1d4ed8;
        }
        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="setup-card">
        <div class="header-badge">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
            <span>Welcome Onboarding</span>
        </div>

        <h1>Set Up Your Business</h1>
        <p class="subtitle">Complete this 1-minute setup to link your Google Reviews page and unlock your employee QR dashboard.</p>

        @if($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('setup.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Company / Brand Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="input-field" value="{{ old('name', $company->name) }}" placeholder="e.g. Deluxe Cafe & Lounge" required>
            </div>

            <div class="form-group">
                <label>Google Review URL <span style="color:#ef4444;">*</span></label>
                <input type="url" name="google_review_url" class="input-field" value="{{ old('google_review_url', $company->google_review_url) }}" placeholder="https://g.page/r/your-place-id/review" required>
                <div class="helper">This is where happy 5-star customers will be directed to post their review.</div>
            </div>

            <div class="form-group">
                <label>Upload Logo (Optional)</label>
                <input type="file" name="logo_file" class="input-field" accept="image/*">
            </div>

            <button type="submit" class="btn-submit">
                <span>Complete Setup & Open Dashboard</span>
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        </form>
    </div>
</body>
</html>
