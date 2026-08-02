@extends('layouts.app')

@section('title', 'Company Locations & Brand Kit · ReviewTracker')

@section('styles')
<style>
  .companies-layout { display: grid; gap: 24px; }
  @media (min-width: 900px) {
    .companies-layout { grid-template-columns: minmax(0, 1.1fr) minmax(0, 1.4fr); }
  }
  .company-item {
    padding: 14px 16px;
    border: 1px solid var(--border-color);
    border-radius: 14px;
    margin-bottom: 12px;
    background: var(--card-bg);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }
  .logo-preview-box {
    width: 80px; height: 80px; border-radius: 16px; overflow: hidden;
    border: 2px solid var(--border-color);
    background: var(--input-bg);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
  }
  .logo-preview-box img { width: 100%; height: 100%; object-fit: contain; }
  .field-group { margin-bottom: 18px; }
  .field-label { display: block; font-size: 13px; font-weight: 700; color: var(--text-heading); margin-bottom: 6px; }
  .helper-note { font-size: 12px; color: var(--text-muted); margin-top: 4px; line-height: 1.4; }
  .color-picker-row { display: flex; align-items: center; gap: 10px; }
  .color-picker-wheel { width: 44px; height: 44px; padding: 2px; border-radius: 10px; cursor: pointer; border: 1px solid var(--border-color); }
  .swatch-circle { width: 28px; height: 28px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.4); cursor: pointer; display: inline-block; transition: transform 0.15s ease; }
  .swatch-circle:hover { transform: scale(1.15); }
</style>
@endsection

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Company Locations & Brand Kit</div>
      <div class="page-subtitle">Manage multi-company locations, upload brand logos, and configure primary review links.</div>
    </div>
  </div>

  <div class="companies-layout">
    <!-- Left Column: Company Locations List & Create New Company -->
    <div>
      <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
          <div>
            <div class="card-kicker">Multi-Company Locations</div>
            <div class="card-title">Your Active Companies</div>
          </div>
          <div class="muted">{{ $companies->count() }} total</div>
        </div>

        <ul style="list-style:none; padding:0; margin:0;">
          @forelse($companies as $c)
          <li class="company-item">
            <div>
              <div style="font-weight:700; color:var(--text-heading);">{{ $c->name }}</div>
              <div class="muted" style="font-size:11px;">ID #{{ $c->id }} @if($c->google_review_url) · 🌐 Configured @else · ⚠️ No Link @endif</div>
            </div>
            @if($currentCompany && $c->id === $currentCompany->id)
              <span class="pill" style="background:#dbeafe; color:#1d4ed8;">Active Location</span>
            @else
              <form action="{{ route('companies.switch') }}" method="POST" style="margin:0;">
                @csrf
                <input type="hidden" name="company_id" value="{{ $c->id }}">
                <button class="btn btn-secondary" type="submit" style="padding:6px 14px; font-size:12px;">Switch Location</button>
              </form>
            @endif
          </li>
          @empty
          <li class="muted">No companies created yet.</li>
          @endforelse
        </ul>
      </div>

      <!-- Add New Location Form -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-kicker">Expansion</div>
            <div class="card-title">+ Add New Company Location</div>
          </div>
        </div>

        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="field-group">
            <label class="field-label">Company Location Name <span style="color:#ef4444;">*</span></label>
            <input class="input" name="name" placeholder="e.g. Acme Cafe - Downtown Branch" required>
            <div class="helper-note">Enter your brand or branch location name.</div>
          </div>

          <div class="field-group">
            <label class="field-label">Upload Brand Logo</label>
            <input class="input" type="file" name="logo_file" accept="image/*">
            <div class="helper-note">Uploading a logo extracts brand color swatches automatically.</div>
          </div>

          <div class="field-group">
            <label class="field-label">Main Google Review URL <span style="color:#ef4444;">*</span></label>
            <input class="input" name="google_review_url" placeholder="https://g.page/r/your-place-id/review" required>
            <div class="helper-note">The Google Maps review destination for positive reviews.</div>
          </div>

          <button class="btn" type="submit" style="width:100%;">Create New Company Location</button>
        </form>
      </div>
    </div>

    <!-- Right Column: Edit Active Company Brand Kit & Colors -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Brand Kit & Review Links</div>
          <div class="card-title">Edit Active Location: {{ $currentCompany?->name }}</div>
        </div>
      </div>

      @if($currentCompany)
      <form action="{{ route('companies.update', $currentCompany) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="field-group">
          <label class="field-label">Company Name <span style="color:#ef4444;">*</span></label>
          <input class="input" name="name" value="{{ $currentCompany->name }}" required>
          <div class="helper-note">The official business name displayed on customer QR landing pages.</div>
        </div>

        <div class="field-group">
          <label class="field-label">Brand Logo Image</label>
          <input class="input" type="file" name="logo_file" accept="image/*" onchange="previewActiveLogo(this)">
          <div class="helper-note">Upload your high-res logo file (PNG/JPG/SVG).</div>
          
          <div style="margin-top:10px; text-align:center;">
            <div class="logo-preview-box">
              @if($currentCompany->logo_url)
                <img id="activeLogoImg" src="{{ $currentCompany->logo_url }}" alt="Logo">
              @else
                <span class="muted" style="font-size:11px;">No Logo Uploaded</span>
              @endif
            </div>
          </div>
        </div>

        <!-- 3 Color Selection Options: Color Wheel + Hex Code Input + Logo Swatches -->
        <div class="field-group">
          <label class="field-label">Primary Brand Color (Pick via Color Wheel, Hex Text, or Logo Swatches)</label>
          <div class="color-picker-row">
            <input type="color" id="primaryWheel" class="color-picker-wheel" value="{{ $currentCompany->primary_color ?? '#0d6efd' }}" onchange="syncPrimaryColor(this.value)">
            <input type="text" id="primaryHexInput" name="primary_color" class="input" value="{{ $currentCompany->primary_color ?? '#0d6efd' }}" placeholder="#0d6efd" oninput="syncPrimaryColor(this.value)">
          </div>
          <div class="helper-note">Extracted Logo Swatches (Click any circle to set primary color):</div>
          <div style="display:flex; gap:8px; margin-top:8px; flex-wrap:wrap;">
            <span class="swatch-circle" style="background:#2563eb" onclick="syncPrimaryColor('#2563eb')"></span>
            <span class="swatch-circle" style="background:#0d6efd" onclick="syncPrimaryColor('#0d6efd')"></span>
            <span class="swatch-circle" style="background:#16a34a" onclick="syncPrimaryColor('#16a34a')"></span>
            <span class="swatch-circle" style="background:#ea580c" onclick="syncPrimaryColor('#ea580c')"></span>
            <span class="swatch-circle" style="background:#9333ea" onclick="syncPrimaryColor('#9333ea')"></span>
            <span class="swatch-circle" style="background:#0f172a" onclick="syncPrimaryColor('#0f172a')"></span>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Secondary Background Color</label>
          <div class="color-picker-row">
            <input type="color" id="secondaryWheel" class="color-picker-wheel" value="{{ $currentCompany->secondary_color ?? '#111827' }}" onchange="syncSecondaryColor(this.value)">
            <input type="text" id="secondaryHexInput" name="secondary_color" class="input" value="{{ $currentCompany->secondary_color ?? '#111827' }}" placeholder="#111827" oninput="syncSecondaryColor(this.value)">
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Main Google Review URL <span style="color:#ef4444;">*</span></label>
          <input class="input" name="google_review_url" value="{{ $currentCompany->google_review_url }}" placeholder="https://g.page/r/your-place-id/review" required>
          <div class="helper-note">Primary destination for positive customer reviews on Google Maps.</div>
        </div>

        <div class="field-group">
          <label class="field-label">TripAdvisor Review URL (Optional)</label>
          <input class="input" name="tripadvisor_review_url" value="{{ $currentCompany->tripadvisor_review_url }}" placeholder="https://www.tripadvisor.com/UserReview-...">
        </div>

        <div class="field-group">
          <label class="field-label">Yelp Review URL (Optional)</label>
          <input class="input" name="yelp_review_url" value="{{ $currentCompany->yelp_review_url }}" placeholder="https://www.yelp.com/biz/...">
        </div>

        <div class="field-group">
          <label class="field-label">Trustpilot Review URL (Optional)</label>
          <input class="input" name="trustpilot_review_url" value="{{ $currentCompany->trustpilot_review_url }}" placeholder="https://www.trustpilot.com/evaluate/...">
          <div class="helper-note">Optional platform links allow positive reviewers to select alternative review destinations.</div>
        </div>

        <button class="btn" type="submit" style="width:100%; padding:14px; font-size:1rem;">Save Brand Kit & Review Links</button>
      </form>
      @else
        <p class="muted">No company selected. Please select or create a location.</p>
      @endif
    </div>
  </div>

  <script>
    function syncPrimaryColor(val) {
      document.getElementById('primaryWheel').value = val;
      document.getElementById('primaryHexInput').value = val;
    }
    function syncSecondaryColor(val) {
      document.getElementById('secondaryWheel').value = val;
      document.getElementById('secondaryHexInput').value = val;
    }
    function previewActiveLogo(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('activeLogoImg').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
@endsection
