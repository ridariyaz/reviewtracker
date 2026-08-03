@extends('layouts.app')

@section('title', 'Help & Searchable Feature Guide · ReviewTracker')

@section('styles')
<style>
  .help-search-hero {
    background: linear-gradient(135deg, var(--primary), #4f46e5);
    color: #ffffff;
    padding: 32px 24px;
    border-radius: 20px;
    margin-bottom: 28px;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.25);
  }
  .help-search-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 8px;
  }
  .help-search-sub {
    font-size: 0.95rem;
    opacity: 0.9;
    margin-bottom: 20px;
    line-height: 1.5;
  }
  .help-search-box {
    position: relative;
    max-width: 600px;
  }
  .help-search-input {
    width: 100%;
    padding: 14px 20px 14px 44px;
    border-radius: 999px;
    border: none;
    background: #ffffff;
    color: #0f172a;
    font-size: 0.95rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
  }
  .help-search-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.5);
  }
  .search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    pointer-events: none;
  }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 28px;
  }
  .feature-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 22px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease;
  }
  .feature-card:hover {
    transform: translateY(-2px);
  }
  .feature-icon {
    width: 40px; height: 40px; border-radius: 12px;
    background: var(--input-bg); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
  }
  .feature-icon svg {
    width: 22px; height: 22px; stroke: currentColor; fill: none; stroke-width: 2;
  }
  .feature-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text-heading);
  }
  .feature-desc {
    font-size: 0.88rem;
    color: var(--text-muted);
    line-height: 1.6;
    flex: 1;
  }
  .feature-explainer {
    margin-top: 14px;
    padding: 10px 12px;
    background: var(--input-bg);
    border-radius: 8px;
    border-left: 3px solid var(--primary);
    font-size: 0.8rem;
    color: var(--text-main);
  }

  .no-results-msg {
    display: none;
    text-align: center;
    padding: 40px;
    color: var(--text-muted);
    font-size: 1rem;
  }
</style>
@endsection

@section('content')
  <div class="help-search-hero">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:16px;">
      <div>
        <div class="help-search-title">Searchable Feature Guide</div>
        <div class="help-search-sub" style="margin-bottom:0;">
          Search any option, settings feature, or setup step below to learn how ReviewTracker works.
        </div>
      </div>
      <button class="btn" style="background:#ffffff; color:#0f172a; font-weight:800;" onclick="startSpotlightTour()">
        <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><polygon points="10 8 16 12 10 16 10 8"></polygon><circle cx="12" cy="12" r="10"></circle></svg>
        <span>Take Feature Tour</span>
      </button>
    </div>

    <div class="help-search-box">
      <svg class="search-icon" viewBox="0 0 24 24" style="width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" id="helpSearchInput" class="help-search-input" placeholder="Search features (e.g., QR, gamification, language, colors, leaderboard, multi-company)..." oninput="filterHelpFeatures()">
    </div>
  </div>

  <div id="noResults" class="no-results-msg">
    🔍 No matching feature guides found. Try searching for "QR", "language", "color", "gamification", or "company".
  </div>

  <div id="featuresGrid" class="features-grid">
    <!-- Feature 1: Multi-Company Locations -->
    <div class="feature-card" data-keywords="multi company locations brand switch active location multiple stores branches">
      <div class="feature-icon">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
      </div>
      <div class="feature-title">Multi-Company Locations</div>
      <div class="feature-desc">
        Manage multiple business branches or brand locations from a single admin account. Switch active company location at any time from the top dropdown or <strong>Companies</strong> page.
      </div>
      <div class="feature-explainer">
        💡 Small Explainer: Each location maintains its own staff directory, brand colors, logo, and Google review destination.
      </div>
    </div>

    <!-- Feature 2: 3 Color Pickers & Logo Extractor -->
    <div class="feature-card" data-keywords="color picker logo swatches primary secondary color wheel hex code palette brand kit">
      <div class="feature-icon">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path></svg>
      </div>
      <div class="feature-title">3 Color Pickers & Logo Swatch Extractor</div>
      <div class="feature-desc">
        Customize primary and secondary customer colors using: 1) Color Wheel, 2) Manual Hex Code input, or 3) Clicking auto-sensed color swatches extracted from your uploaded logo image!
      </div>
      <div class="feature-explainer">
        💡 Small Explainer: Click any extracted color swatch circle on the Companies or Settings page to populate hex inputs instantly.
      </div>
    </div>

    <!-- Feature 3: Customer Review Gamification Contest -->
    <div class="feature-card" data-keywords="gamification contest lottery lucky winner prize voucher threshold reward scanner customer gift">
      <div class="feature-icon">
        <svg viewBox="0 0 24 24"><path d="M20 12v10H4V12"></path><path d="M22 7H2v5h20V7z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
      </div>
      <div class="feature-title">Customer Review Gamification Contest</div>
      <div class="feature-desc">
        Turn on the Review Lottery in Settings. Set a winner interval (e.g. every 50th scan) and gift description. The threshold reviewer triggers an instant <strong>🎉 Lucky Winner Claim Code</strong> card!
      </div>
      <div class="feature-explainer">
        💡 Small Explainer: Set interval to 1 in settings for instant live testing during demo.
      </div>
    </div>

    <!-- Feature 4: Multi-Language QR Generation -->
    <div class="feature-card" data-keywords="language qr english malayalam arabic hindi bengali bangladeshi employee dashboard qr scan translate">
      <div class="feature-icon">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
      </div>
      <div class="feature-title">Multi-Language Employee QR Generator</div>
      <div class="feature-desc">
        Employees can select customer language (English, Malayalam, Arabic, Hindi, Bengali) on their dashboard to generate customized QR codes for international customers.
      </div>
      <div class="feature-explainer">
        💡 Small Explainer: QR codes with `?lang=ml` display customer screens in Malayalam while tracking scans to the same employee.
      </div>
    </div>

    <!-- Feature 5: Sleek Printable Counter Standees -->
    <div class="feature-card" data-keywords="print download standee poster table pdf counter avatar logo employee qr display">
      <div class="feature-icon">
        <svg viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
      </div>
      <div class="feature-title">Printable Counter Standees & Posters</div>
      <div class="feature-desc">
        Open `/employee/qr` and click <strong>"Print / Download Standee"</strong> to save high-resolution A4 / Letter table standee cards with company logo, brand colors, employee avatar, and QR code.
      </div>
      <div class="feature-explainer">
        💡 Small Explainer: Uses CSS `@media print` rules for clean margin-free printing and PDF export.
      </div>
    </div>

    <!-- Feature 6: Human SEO Review Generator -->
    <div class="feature-card" data-keywords="review generator seo keywords industry suggested human copy text clipboard pop-up blocker">
      <div class="feature-icon">
        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
      </div>
      <div class="feature-title">Combinatorial Human Review Suggestions</div>
      <div class="feature-desc">
        Generates realistic 2–3 sentence reviews incorporating staff name, local SEO terms, and company industry keywords. Customers copy the text in 1 tap and open Google.
      </div>
      <div class="feature-explainer">
        💡 Small Explainer: Bypasses mobile Safari pop-up blockers by executing clipboard copy synchronously before opening Google.
      </div>
    </div>
  </div>

  <script>
    function filterHelpFeatures() {
      const query = document.getElementById('helpSearchInput').value.toLowerCase().trim();
      const cards = document.querySelectorAll('.feature-card');
      let visibleCount = 0;

      cards.forEach(card => {
        const keywords = card.getAttribute('data-keywords') || '';
        const title = card.querySelector('.feature-title').textContent.toLowerCase();
        const desc = card.querySelector('.feature-desc').textContent.toLowerCase();

        if (!query || keywords.includes(query) || title.includes(query) || desc.includes(query)) {
          card.style.display = 'flex';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      document.getElementById('noResults').style.display = (visibleCount === 0) ? 'block' : 'none';
    }
  </script>
@endsection
