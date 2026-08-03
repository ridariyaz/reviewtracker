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
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(ellipse at top, var(--secondary), #020617 80%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
            -webkit-font-smoothing: antialiased;
        }
        .card {
            max-width: 480px;
            width: 100%;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            border-radius: 24px;
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }
        .logo-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.2);
            margin: 0 auto 16px;
            display: block;
            background: #000;
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
            font-size: 0.85rem;
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
            font-size: 0.92rem;
            margin-bottom: 24px;
            line-height: 1.5;
            color: #cbd5e1;
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
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }
        .btn:active { transform: scale(0.98); }
        .btn-primary { background: var(--primary); color: #ffffff; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3); }
        .btn-primary:hover { opacity: 0.92; }
        .btn-secondary { background: rgba(255, 255, 255, 0.1); color: #e2e8f0; border: 1px solid rgba(255, 255, 255, 0.15); }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.18); }
        .toast {
            position: fixed; bottom: 24px; left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #10b981; color: #ffffff; padding: 12px 24px;
            border-radius: 30px; font-weight: 600; font-size: 0.95rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3); opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none; z-index: 1000;
        }
        .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        @media (max-width: 480px) {
            body { padding: 12px 10px; }
            .card { padding: 22px 16px; border-radius: 20px; }
            .logo-circle { width: 68px; height: 68px; margin-bottom: 12px; }
            h1 { font-size: 1.25rem; }
            p.tagline { font-size: 0.85rem; margin-bottom: 18px; }
            .btn { padding: 14px 16px; font-size: 0.95rem; }
            .review-textarea { min-height: 110px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <div class="card">
        @if($brandLogoUrl)
            <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" class="logo-circle">
        @endif

        @if($isWinner)
            <!-- Winner Banner Written Directly on Top of the Page -->
            <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(234, 179, 8, 0.15)); border: 2px solid #f59e0b; border-radius: 18px; padding: 20px; margin-bottom: 24px; text-align: center;">
                <div style="font-size:0.8rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#facc15; margin-bottom:4px;">Review Contest Winner</div>
                <h2 style="font-size:1.35rem; font-weight:800; color:#ffffff; margin-bottom:6px;">You Won: {{ $gamificationReward }}</h2>
                
                @if(!empty($gamificationImageUrl))
                    <div style="margin: 12px 0; border-radius: 12px; overflow: hidden; max-height: 140px; border: 1px solid rgba(255,255,255,0.2);">
                        <img src="{{ $gamificationImageUrl }}" alt="Prize" style="width:100%; height:140px; object-fit:contain; background:#000;">
                    </div>
                @endif

                <div style="background:#f59e0b; color:#000; font-weight:800; font-size:1.15rem; padding:8px 18px; border-radius:999px; display:inline-block; margin:8px 0; letter-spacing:1px;">
                    CLAIM CODE: {{ $winnerCode }}
                </div>
                
                <p style="font-size:0.85rem; color:#e2e8f0; margin-top:8px; line-height:1.4;">
                    Assisted by <strong>{{ $employee->name ?? 'our staff' }}</strong>. Complete your Google Review below and show our staff to claim your prize!
                </p>
            </div>
        @endif

        <div class="badge-row">
            <div class="badge">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:currentColor;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                <span>Great Experience</span>
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
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        <span>Copy</span>
                    </button>
                    <button type="button" class="btn-regenerate" id="regenerateBtn" onclick="generateNewReview()">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                        <span>Generate Another</span>
                    </button>
                </div>
            </div>
            <textarea id="reviewText" class="review-textarea" rows="4"></textarea>
        </div>

        <div class="actions">
            <button type="button" class="btn btn-primary" onclick="useSuggestedReview()">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                <span>Copy Review & Open Google</span>
            </button>
            <a href="{{ $googleReviewUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                <span>Write My Own Review on Google</span>
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>
    </div>

    <div id="toast" class="toast">Review copied to clipboard!</div>

    <script>
        const employeeName = @json($employee->name ?? 'the team');
        const companyName = @json($brandName);
        const googleReviewUrl = @json($googleReviewUrl);
        const industry = @json($industry ?? '');
        const rawKeywords = @json($keywords ?? '');

        const customKeywordsList = rawKeywords ? rawKeywords.split(',').map(k => k.trim()).filter(Boolean) : [];

        // Category specific review scenario generators
        const categoryScenarios = {
            'Restaurant & Dining': [
                'The food came out fresh and hot and the table service was super prompt.',
                'Great atmosphere, delicious meal, and really friendly attentive staff.',
                'The food quality was top notch and everything was served quickly.'
            ],
            'Automotive & Repair': [
                'They diagnosed the issue quickly and explained everything clearly before doing any work.',
                'Fast turnaround time, fair transparent pricing, and my car is running perfectly.',
                'Honest advice, quick inspection, and very professional service overall.'
            ],
            'Beauty & Salon': [
                'The place is spotless and super relaxing, and they really paid attention to detail.',
                'Felt super comfortable from start to finish and I walked out really happy with the results.',
                'Great customer care, clean space, and helpful beauty recommendations.'
            ],
            'Medical & Dental': [
                'Very gentle care, clean facility, and the front desk made check-in effortless.',
                'They took time to explain everything clearly and made sure I felt comfortable throughout.',
                'Punctual appointment times, polite staff, and very professional care.'
            ],
            'Fitness & Wellness': [
                'Clean facilities, great equipment, and a very welcoming environment.',
                'Attentive staff, clean locker rooms, and great positive energy all around.'
            ],
            'Home & Trades Services': [
                'Punctual, clean work, and they solved the problem much faster than expected.',
                'Very polite, respectful of the property, and explained the fix clearly.'
            ],
            'Professional Services': [
                'Clear communication, fast turnaround, and they handled everything thoroughly.',
                'Very knowledgeable team that saved me time and gave great guidance.'
            ]
        };

        function generateNewReview() {
            // 50% chance to mention employee name, 50% chance general team praise
            const mentionStaff = Math.random() > 0.5;

            const staffOpenings = [
                `Popped into ${companyName} today and ${employeeName} helped me out right away.`,
                `Had a really smooth experience at ${companyName} thanks to ${employeeName}.`,
                `Stopped by ${companyName} earlier and ${employeeName} provided awesome customer service.`,
                `Huge thanks to ${employeeName} at ${companyName} for all the help today.`,
                `Visited ${companyName} and had a great experience with ${employeeName}.`,
                `If you're heading to ${companyName}, definitely ask for ${employeeName}.`,
                `Just left ${companyName} and wanted to leave a quick shoutout for ${employeeName}.`,
                `Great experience at ${companyName} today. ${employeeName} was super helpful.`
            ];

            const generalOpenings = [
                `Popped into ${companyName} today and the service was fantastic right away.`,
                `Had a really smooth experience at ${companyName} from start to finish.`,
                `Stopped by ${companyName} earlier and received awesome customer service.`,
                `Visited ${companyName} today and was really impressed by the team.`,
                `Just left ${companyName} and wanted to share how great the visit was.`,
                `Great experience at ${companyName} today. Everyone was super helpful.`
            ];

            const generalDetails = [
                'They answered all my questions patiently and helped me pick the best option without being pushy.',
                'The place was clean, well organized, and the pricing here is super fair.',
                'Super knowledgeable staff, gave me honest recommendations, and got everything sorted in under 5 minutes.',
                'They have a great selection of top quality options at reasonable prices.',
                'Quick checkout, fair pricing, and really attentive service.',
                'Very polite, efficient, and made the whole process quick and easy.'
            ];

            const closings = [
                'Definitely my new go to local spot. Highly recommend!',
                'Awesome customer service and fair prices. Easily 5 stars.',
                'Easily the best customer service in the area. Will definitely be back!',
                'Clean environment, fast service, and great value. Highly recommended!',
                'Top notch service from start to finish. I will be returning for sure.',
                'Great local business with fantastic staff. Would recommend to anyone.'
            ];

            const openingList = mentionStaff ? staffOpenings : generalOpenings;
            let openSentence = getRandomElement(openingList);

            let detailList = [...generalDetails];
            if (industry && categoryScenarios[industry]) {
                detailList = detailList.concat(categoryScenarios[industry]);
            }

            if (customKeywordsList.length > 0) {
                customKeywordsList.forEach(kw => {
                    detailList.push(`Special thanks for the ${kw}, really appreciated the attention to detail.`);
                    detailList.push(`I was impressed by the ${kw} and how smooth everything went.`);
                });
            }

            let detailSentence = getRandomElement(detailList);
            let closeSentence = getRandomElement(closings);

            let reviewText = `${openSentence} ${detailSentence} ${closeSentence}`;

            // Clean all dashes and double spaces
            reviewText = reviewText.replace(/[-—]/g, '').replace(/\s+/g, ' ').trim();

            document.getElementById('reviewText').value = reviewText;
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => { toast.classList.remove('show'); }, 3000);
        }

        function openGoogleInstant() {
            if (!googleReviewUrl) return;
            const win = window.open(googleReviewUrl, '_blank');
            if (!win || win.closed || typeof win.closed === 'undefined') {
                window.location.href = googleReviewUrl;
            }
        }

        function copyOnlyText() {
            const text = document.getElementById('reviewText').value;
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text);
                } else {
                    const textarea = document.getElementById('reviewText');
                    textarea.select();
                    document.execCommand('copy');
                }
            } catch (e) {}
            showToast('Review text copied to clipboard!');
        }

        function useSuggestedReview() {
            const text = document.getElementById('reviewText').value;
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text);
                } else {
                    const textarea = document.getElementById('reviewText');
                    textarea.select();
                    document.execCommand('copy');
                }
            } catch (e) {}

            showToast('Copied! Opening Google Reviews...');
            openGoogleInstant();
        }

        generateNewReview();
    </script>
</body>
</html>
