<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brandName }} - Leave a Google Review</title>
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
            max-width: 480px;
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
        .badge-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .star-badge {
            color: #f59e0b;
            font-size: 1rem;
            letter-spacing: 2px;
        }
        h1 {
            font-size: 1.45rem;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        p.tagline {
            opacity: 0.85;
            font-size: 0.95rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .review-box-wrapper {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }
        .review-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 0 4px;
        }
        .review-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 700;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-inline-copy {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-inline-copy:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .btn-regenerate {
            background: transparent;
            border: none;
            color: #60a5fa;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 6px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-regenerate:hover {
            background: rgba(96, 165, 250, 0.15);
            color: #93c5fd;
        }
        .review-textarea {
            width: 100%;
            min-height: 130px;
            background: rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            padding: 14px;
            color: #f8fafc;
            font-size: 0.95rem;
            line-height: 1.55;
            resize: vertical;
            font-family: inherit;
            transition: border-color 0.2s ease;
        }
        .review-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .btn {
            width: 100%;
            padding: 16px 20px;
            border-radius: 14px;
            border: none;
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.15s ease, opacity 0.15s ease, box-shadow 0.15s ease;
        }
        .btn:active {
            transform: scale(0.98);
        }
        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }
        .btn-primary:hover {
            opacity: 0.92;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.18);
        }
        .toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #10b981;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
            z-index: 1000;
        }
        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="card">
        @if($brandLogoUrl)
            <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="logo">
        @endif

        <div class="badge-row">
            <div class="badge">
                <span>✨</span> Great Experience
            </div>
            <div class="star-badge">★★★★★</div>
        </div>

        <h1>Thank you for your feedback!</h1>
        <p class="tagline">We'd love for you to share your experience on Google. Use our suggested review below or write your own!</p>

        <div class="review-box-wrapper">
            <div class="review-label-row">
                <span class="review-label">Suggested Review</span>
                <div class="header-actions">
                    <button type="button" class="btn-inline-copy" onclick="copyOnlyText()">
                        <span>📋</span> Copy
                    </button>
                    <button type="button" class="btn-regenerate" id="regenerateBtn" onclick="generateNewReview()">
                        <span>✨</span> Generate Another
                    </button>
                </div>
            </div>
            <textarea id="reviewText" class="review-textarea" rows="4"></textarea>
        </div>

        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="useSuggestedReview()">
                <span>📋</span> Copy Review & Open Google
            </button>
            <a href="{{ $googleReviewUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                Write My Own Review on Google &rarr;
            </a>
        </div>
    </div>

    <div id="toast" class="toast">Review copied to clipboard!</div>

    <script>
        const employeeName = @json($employee->name);
        const companyName = @json($brandName);
        const googleReviewUrl = @json($googleReviewUrl);

        // Realistic, natural, human customer review sentences with Local SEO keywords
        const openings = [
            `Popped into ${companyName} today and ${employeeName} helped me out right away.`,
            `Had a really smooth experience at ${companyName} thanks to ${employeeName}.`,
            `Stopped by ${companyName} earlier and ${employeeName} provided awesome customer service.`,
            `Huge thanks to ${employeeName} at ${companyName} for all the help today!`,
            `Visited ${companyName} and had a great experience with ${employeeName}.`,
            `If you're heading to ${companyName}, definitely ask for ${employeeName}.`,
            `Just left ${companyName} and wanted to leave a quick shoutout for ${employeeName}.`,
            `Great experience buying from ${companyName} today. ${employeeName} was super helpful.`
        ];

        const details = [
            `They answered all my questions patiently and helped me pick the best option without being pushy.`,
            `The store was clean, well organized, and the prices here are super fair.`,
            `Super knowledgeable, gave me honest recommendations, and got everything sorted in under 5 minutes.`,
            `They have a great selection of top quality products at reasonable prices.`,
            `Quick checkout, fair pricing, and really attentive staff.`,
            `They listened to what I needed, saved me time, and made sure I got top quality.`,
            `Very polite, efficient, and made the whole process quick and easy.`,
            `They really know their stuff and gave me honest advice on what to get.`
        ];

        const closings = [
            `Definitely my new go-to local shop. Highly recommend!`,
            `Awesome customer service and fair prices — 5 stars!`,
            `Easily the best customer service in the area. Will definitely be back!`,
            `Clean store, fast service, and great prices. Highly recommended!`,
            `Top-notch service from start to finish. I'll be returning for sure!`,
            `Great local store with fantastic staff. 10/10!`
        ];

        // Track generated review hashes to NEVER repeat the same review twice in a session
        const seenReviews = new Set();

        function getRandomElement(arr) {
            return arr[Math.floor(Math.random() * arr.length)];
        }

        function generateNewReview() {
            let candidate = '';
            let attempts = 0;

            do {
                attempts++;
                const open = getRandomElement(openings);
                const detail = getRandomElement(details);
                const close = getRandomElement(closings);

                candidate = `${open} ${detail} ${close}`;

                if (attempts > 200) {
                    seenReviews.clear();
                    break;
                }
            } while (seenReviews.has(candidate));

            seenReviews.add(candidate);
            document.getElementById('reviewText').value = candidate;
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function copyOnlyText() {
            const text = document.getElementById('reviewText').value;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text);
            } else {
                const textarea = document.getElementById('reviewText');
                textarea.select();
                document.execCommand('copy');
            }
            showToast('Review text copied to clipboard!');
        }

        function useSuggestedReview() {
            const text = document.getElementById('reviewText').value;
            
            // Perform clipboard copy synchronously on user tap
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text);
            } else {
                const textarea = document.getElementById('reviewText');
                textarea.select();
                document.execCommand('copy');
            }

            showToast('Copied! Navigating to Google Reviews...');

            // Open Google Review page synchronously or via window.open immediately
            const win = window.open(googleReviewUrl, '_blank');
            if (!win) {
                // If mobile browser blocked pop-up, fallback to direct location change
                window.location.href = googleReviewUrl;
            }
        }

        // Initialize with first authentic human-style review
        generateNewReview();
    </script>
</body>
</html>
