@extends('layouts.app')

@section('title', 'Help & Explainer Guide · ReviewTracker')

@section('styles')
<style>
  .help-hero { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; padding: 28px; border-radius: 16px; margin-bottom: 24px; box-shadow: 0 10px 25px rgba(15,23,42,0.15); }
  .help-hero-title { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
  .help-hero-sub { font-size: 14px; color: #94a3b8; max-width: 650px; line-height: 1.5; }
  
  .steps-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px; }
  .step-card { background: var(--card-bg); border-radius: 14px; padding: 22px; border: 1px solid rgba(148, 163, 184, 0.25); box-shadow: 0 4px 14px rgba(15,23,42,0.05); display: flex; flex-direction: column; }
  .step-num { width: 36px; height: 36px; border-radius: 999px; background: #e0e7ff; color: #4338ca; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 16px; margin-bottom: 14px; }
  .step-title { font-size: 16px; font-weight: 600; margin-bottom: 8px; color: var(--text-main); }
  .step-desc { font-size: 13px; color: var(--text-muted); line-height: 1.6; flex: 1; }
  .step-tip { margin-top: 14px; padding: 10px 12px; background: #f8fafc; border-radius: 8px; border-left: 3px solid var(--primary); font-size: 12px; color: #334155; }
  
  .faq-card { background: var(--card-bg); border-radius: 14px; padding: 20px 24px; border: 1px solid rgba(148, 163, 184, 0.25); }
  .faq-title { font-size: 18px; font-weight: 600; margin-bottom: 16px; }
  .faq-item { margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; }
  .faq-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
  .faq-q { font-weight: 600; font-size: 14px; margin-bottom: 4px; color: #1e293b; }
  .faq-a { font-size: 13px; color: var(--text-muted); line-height: 1.5; }
</style>
@endsection

@section('content')
  <div class="help-hero">
    <div class="help-hero-title">📖 How ReviewTracker Works</div>
    <div class="help-hero-sub">
      ReviewTracker empowers your business to capture 5-star Google reviews from satisfied customers while keeping neutral or negative feedback private for internal resolution.
    </div>
  </div>

  <div class="steps-grid">
    <!-- Step 1 -->
    <div class="step-card">
      <div class="step-num">1</div>
      <div class="step-title">Configure Brand & Google Link</div>
      <div class="step-desc">
        Set up your company name, upload your brand logo, and paste your official <strong>Google Maps / Google Review URL</strong>.
      </div>
      <div class="step-tip">
        📍 <strong>How to find your Google URL:</strong> Search your business on Google Maps, click "Ask for reviews", and copy the short link!
      </div>
    </div>

    <!-- Step 2 -->
    <div class="step-card">
      <div class="step-num">2</div>
      <div class="step-title">Add Staff & Print QR Codes</div>
      <div class="step-desc">
        Add your employees on the Dashboard. Each staff member gets a unique QR code linked directly to their profile.
      </div>
      <div class="step-tip">
        📲 <strong>Tip:</strong> Employees can download their QR or open it fullscreen on their smartphone to show customers at checkout or tables.
      </div>
    </div>

    <!-- Step 3 -->
    <div class="step-card">
      <div class="step-num">3</div>
      <div class="step-title">Smart Customer Review Funnel</div>
      <div class="step-desc">
        When a customer scans a staff QR code:
        <ul style="margin:6px 0 0 16px;padding:0;">
          <li><strong>Good (5-Star):</strong> Instantly opens your Google Review page.</li>
          <li><strong>OK / Bad:</strong> Collects private comments sent to your inbox.</li>
        </ul>
      </div>
      <div class="step-tip">
        🛡️ Protects your public Google rating while capturing private customer feedback.
      </div>
    </div>

    <!-- Step 4 -->
    <div class="step-card">
      <div class="step-num">4</div>
      <div class="step-title">Inbox & Conversion Analytics</div>
      <div class="step-desc">
        Track customer conversion rates, daily scan trends, and employee performance matrices on the <strong>Analytics</strong> page.
      </div>
      <div class="step-tip">
        📊 Export employee stats and feedback history to CSV at any time.
      </div>
    </div>
  </div>

  <!-- FAQ Section -->
  <div class="faq-card">
    <div class="faq-title">Frequently Asked Questions</div>
    
    <div class="faq-item">
      <div class="faq-q">Do customers need to download an app to scan the QR code?</div>
      <div class="faq-a">No! Customers simply open their standard smartphone camera app, point it at the QR code, and tap the link that appears. It works on iOS and Android with zero downloads required.</div>
    </div>

    <div class="faq-item">
      <div class="faq-q">How do staff members log into their personal dashboard?</div>
      <div class="faq-a">Admin users can set a custom login username & password for any employee on the Admin Dashboard under "Actions" (⋯) $\rightarrow$ "Set login". Staff can then log in at <code>/employee/login</code> on their mobile phones.</div>
    </div>

    <div class="faq-item">
      <div class="faq-q">Where do private "OK" or "Bad" feedback comments go?</div>
      <div class="faq-a">They land in your private <strong>Feedback Inbox</strong> in the admin panel. Managers can mark status as <em>New</em>, <em>In Progress</em>, or <em>Resolved</em>.</div>
    </div>
  </div>
@endsection
