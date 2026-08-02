<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brandName }} - Tell us more</title>
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
            max-height: 60px;
            margin-bottom: 16px;
            object-fit: contain;
        }
        h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        p.tagline {
            opacity: 0.8;
            font-size: 0.95rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .chip-section-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 12px;
            text-align: left;
        }
        .chips-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        .chip {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #e2e8f0;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .chip:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .chip.selected {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }
        textarea {
            width: 100%;
            min-height: 100px;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            padding: 14px;
            color: #f8fafc;
            font-size: 0.95rem;
            line-height: 1.5;
            resize: vertical;
            font-family: inherit;
            margin-bottom: 20px;
            transition: border-color 0.2s ease;
        }
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
        }
        button[type="submit"] {
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            transition: transform 0.15s ease, opacity 0.15s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }
        button[type="submit"]:active {
            transform: scale(0.98);
        }
        button[type="submit"]:hover {
            opacity: 0.92;
        }
    </style>
</head>
<body>
    <div class="card">
        @if($brandLogoUrl)
            <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="logo">
        @endif

        <h1>We're sorry to hear that. What happened?</h1>
        <p class="tagline">Your feedback goes directly to {{ $brandName }} management so we can make things right.</p>

        <form action="{{ route('review.submit') }}" method="POST" id="feedbackForm">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" name="rating" value="{{ $rating }}">

            <div class="chip-section-title">Select Issue (1-Tap Quick Select)</div>
            <div class="chips-grid">
                <div class="chip" data-issue="Long Wait Time" onclick="toggleChip(this)">
                    <span>⏱️</span> Long Wait Time
                </div>
                <div class="chip" data-issue="Staff Communication" onclick="toggleChip(this)">
                    <span>💬</span> Communication
                </div>
                <div class="chip" data-issue="Pricing / Value" onclick="toggleChip(this)">
                    <span>💵</span> Pricing / Value
                </div>
                <div class="chip" data-issue="Item Out of Stock" onclick="toggleChip(this)">
                    <span>📦</span> Item Out of Stock
                </div>
                <div class="chip" data-issue="Store Cleanliness" onclick="toggleChip(this)">
                    <span>🧹</span> Cleanliness
                </div>
                <div class="chip" data-issue="General Experience" onclick="toggleChip(this)">
                    <span>😕</span> Other Issue
                </div>
            </div>

            <textarea name="comment" id="commentBox" placeholder="Optional: Tell us more about what went wrong..."></textarea>
            <button type="submit">Submit Private Feedback</button>
        </form>
    </div>

    <script>
        const selectedIssues = new Set();

        function toggleChip(element) {
            const issue = element.getAttribute('data-issue');
            if (selectedIssues.has(issue)) {
                selectedIssues.delete(issue);
                element.classList.remove('selected');
            } else {
                selectedIssues.add(issue);
                element.classList.add('selected');
            }

            updateCommentField();
        }

        function updateCommentField() {
            const commentBox = document.getElementById('commentBox');
            let currentText = commentBox.value;

            // Remove previous auto-generated tag header if present
            if (currentText.startsWith('[Issue: ')) {
                const endBracketIndex = currentText.indexOf(']\n');
                if (endBracketIndex !== -1) {
                    currentText = currentText.substring(endBracketIndex + 2);
                }
            }

            if (selectedIssues.size > 0) {
                const issuesText = Array.from(selectedIssues).join(', ');
                commentBox.value = `[Issue: ${issuesText}]\n${currentText.trim()}`;
            } else {
                commentBox.value = currentText.trim();
            }
        }
    </script>
</body>
</html>
