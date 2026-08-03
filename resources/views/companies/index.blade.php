@extends('layouts.app')

@section('title', 'Manage Companies · ReviewTracker')

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
  .swatch-circle { width: 32px; height: 32px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.4); cursor: pointer; display: inline-block; transition: transform 0.15s ease; box-shadow:0 2px 6px rgba(0,0,0,0.15); }
  .swatch-circle:hover { transform: scale(1.18); }
  .btn-add-platform {
    background: var(--input-bg);
    border: 1px dashed var(--primary);
    color: var(--primary);
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
  }
  .custom-link-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
  .btn-remove-link { background: #ef4444; color: #fff; border: none; padding: 10px 14px; border-radius: 8px; font-weight: 700; cursor: pointer; }
</style>
@endsection

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Manage Companies</div>
      <div class="page-subtitle">Manage multiple distinct companies under your admin account, set up brand kits, and configure Google review links.</div>
    </div>
  </div>

  <div class="companies-layout">
    <!-- Left Column: Companies List & Create New Company -->
    <div>
      <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
          <div>
            <div class="card-kicker">Multi-Company Management</div>
            <div class="card-title">Your Companies</div>
          </div>
          <div class="muted">{{ $companies->count() }} total</div>
        </div>

        <ul style="list-style:none; padding:0; margin:0;">
          @forelse($companies as $c)
          <li class="company-item">
            <div>
              <div style="font-weight:700; color:var(--text-heading);">{{ $c->name }}</div>
              <div class="muted" style="font-size:11px;">ID #{{ $c->id }} @if($c->google_review_url) · 🌐 Google Review Configured @else · ⚠️ No Link @endif</div>
            </div>
            @if($currentCompany && $c->id === $currentCompany->id)
              <span class="pill" style="background:#dbeafe; color:#1d4ed8;">Active Company</span>
            @else
              <form action="{{ route('companies.switch') }}" method="POST" style="margin:0;">
                @csrf
                <input type="hidden" name="company_id" value="{{ $c->id }}">
                <button class="btn btn-secondary" type="submit" style="padding:6px 14px; font-size:12px;">Switch Company</button>
              </form>
            @endif
          </li>
          @empty
          <li class="muted">No companies created yet.</li>
          @endforelse
        </ul>
      </div>

      <!-- Add New Company Form -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-kicker">New Business</div>
            <div class="card-title">+ Add New Company</div>
          </div>
        </div>

        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="field-group">
            <label class="field-label">Company Name <span style="color:#ef4444;">*</span></label>
            <input class="input" name="name" placeholder="e.g. Apex Dental Care" required>
            <div class="helper-note">Enter the name of your business or brand.</div>
          </div>

          <div class="field-group">
            <label class="field-label">Upload Company Logo</label>
            <input class="input" type="file" name="logo_file" accept="image/*" onchange="extractClientColors(this, 'create')">
            <div class="helper-note">Selecting a logo immediately extracts brand color swatches below!</div>
            
            <div id="createSwatchesContainer" style="display:none; margin-top:10px;">
              <div class="helper-note" style="font-weight:700; color:var(--text-heading);">✨ Instantly Extracted Logo Colors (Click to set primary color):</div>
              <div id="createSwatchesRow" style="display:flex; gap:8px; margin-top:6px; flex-wrap:wrap;"></div>
            </div>
          </div>

          <div class="field-group">
            <label class="field-label">Primary Brand Color</label>
            <div class="color-picker-row">
              <input type="color" id="createPrimaryWheel" class="color-picker-wheel" value="#0d6efd" onchange="syncCreateColor('primary', this.value)">
              <input type="text" id="createPrimaryHex" name="primary_color" class="input" value="#0d6efd" placeholder="#0d6efd" oninput="syncCreateColor('primary', this.value)">
            </div>
          </div>

          <div class="field-group">
            <label class="field-label">Main Google Review URL <span style="color:#ef4444;">*</span></label>
            <input class="input" name="google_review_url" placeholder="https://g.page/r/your-place-id/review" required>
            <div class="helper-note">The primary Google Maps review link where 5-star customers will be directed.</div>
          </div>

          <button class="btn" type="submit" style="width:100%;">Create Company</button>
        </form>
      </div>
    </div>

    <!-- Right Column: Edit Active Company Brand Kit & Colors -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Brand Kit & Review Links</div>
          <div class="card-title">Edit Active Company: {{ $currentCompany?->name }}</div>
        </div>
      </div>

      @if($currentCompany)
      <form action="{{ route('companies.update', $currentCompany) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="field-group">
          <label class="field-label">Company Name <span style="color:#ef4444;">*</span></label>
          <input class="input" name="name" value="{{ $currentCompany->name }}" required>
          <div class="helper-note">The official business name displayed on customer QR landing pages and printable standees.</div>
        </div>

        <div class="field-group">
          <label class="field-label">Company Logo Image</label>
          <input class="input" type="file" name="logo_file" accept="image/*" onchange="extractClientColors(this, 'edit')">
          <div class="helper-note">Selecting a new logo file instantly extracts brand color swatches below!</div>
          
          <div style="margin-top:10px; text-align:center;">
            <div class="logo-preview-box">
              @if($currentCompany->logo_url)
                <img id="activeLogoImg" src="{{ $currentCompany->logo_url }}" alt="Logo">
              @else
                <span class="muted" style="font-size:11px;">No Logo Uploaded</span>
              @endif
            </div>
          </div>

          <!-- Instant Extracted Logo Swatches Box -->
          <div id="editSwatchesContainer" style="margin-top:10px;">
            <div class="helper-note" style="font-weight:700; color:var(--text-heading);">✨ Extracted Logo Colors (Click any circle to set primary color):</div>
            <div id="editSwatchesRow" style="display:flex; gap:8px; margin-top:6px; flex-wrap:wrap;">
              <span class="swatch-circle" style="background:#2563eb" onclick="syncEditPrimary('#2563eb')"></span>
              <span class="swatch-circle" style="background:#0d6efd" onclick="syncEditPrimary('#0d6efd')"></span>
              <span class="swatch-circle" style="background:#16a34a" onclick="syncEditPrimary('#16a34a')"></span>
              <span class="swatch-circle" style="background:#ea580c" onclick="syncEditPrimary('#ea580c')"></span>
              <span class="swatch-circle" style="background:#9333ea" onclick="syncEditPrimary('#9333ea')"></span>
              <span class="swatch-circle" style="background:#0f172a" onclick="syncEditPrimary('#0f172a')"></span>
            </div>
          </div>
        </div>

        <!-- 3 Color Selection Options: Color Wheel + Hex Text + Logo Swatches -->
        <div class="field-group">
          <label class="field-label">Primary Brand Color</label>
          <div class="color-picker-row">
            <input type="color" id="editPrimaryWheel" class="color-picker-wheel" value="{{ $currentCompany->primary_color ?? '#0d6efd' }}" onchange="syncEditPrimary(this.value)">
            <input type="text" id="editPrimaryHex" name="primary_color" class="input" value="{{ $currentCompany->primary_color ?? '#0d6efd' }}" placeholder="#0d6efd" oninput="syncEditPrimary(this.value)">
          </div>
          <div class="helper-note">Pick via color wheel, hex code text, or extracted logo swatches above.</div>
        </div>

        <div class="field-group">
          <label class="field-label">Secondary Background Color</label>
          <div class="color-picker-row">
            <input type="color" id="editSecondaryWheel" class="color-picker-wheel" value="{{ $currentCompany->secondary_color ?? '#111827' }}" onchange="syncEditSecondary(this.value)">
            <input type="text" id="editSecondaryHex" name="secondary_color" class="input" value="{{ $currentCompany->secondary_color ?? '#111827' }}" placeholder="#111827" oninput="syncEditSecondary(this.value)">
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Main Google Review URL <span style="color:#ef4444;">*</span></label>
          <input class="input" name="google_review_url" value="{{ $currentCompany->google_review_url }}" placeholder="https://g.page/r/your-place-id/review" required>
          <div class="helper-note">Primary destination for positive customer reviews on Google Maps.</div>
        </div>

        <!-- Clean Dynamic Review Links Section with + Add Review Link Button -->
        <div class="field-group">
          <label class="field-label">Additional Review Platforms (Click + Add Review Link to add custom sites)</label>
          <div id="companyCustomLinks">
            @if(is_array($currentCompany->custom_links))
              @foreach($currentCompany->custom_links as $link)
                <div class="custom-link-row">
                  <input type="text" name="custom_link_name[]" class="input" style="width:35%;" value="{{ $link['name'] ?? '' }}" placeholder="Platform Name (e.g. Yelp, TripAdvisor)">
                  <input type="url" name="custom_link_url[]" class="input" style="width:55%;" value="{{ $link['url'] ?? '' }}" placeholder="https://...">
                  <button type="button" class="btn-remove-link" onclick="this.parentElement.remove()">✕</button>
                </div>
              @endforeach
            @endif
          </div>

          <button type="button" class="btn-add-platform" onclick="addCompanyCustomLink()">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>+ Add Review Link</span>
          </button>
          <div class="helper-note">Add extra review destinations if you want customers to choose between Google and other platforms.</div>
        </div>

        <button class="btn" type="submit" style="width:100%; padding:14px; font-size:1rem;">Save Company Brand Kit & Links</button>
      </form>
      @else
        <p class="muted">No company selected. Please select or create a company.</p>
      @endif
    </div>
  </div>

  <script>
    function syncCreateColor(type, val) {
      if (type === 'primary') {
        document.getElementById('createPrimaryWheel').value = val;
        document.getElementById('createPrimaryHex').value = val;
      }
    }
    function syncEditPrimary(val) {
      document.getElementById('editPrimaryWheel').value = val;
      document.getElementById('editPrimaryHex').value = val;
    }
    function syncEditSecondary(val) {
      document.getElementById('editSecondaryWheel').value = val;
      document.getElementById('editSecondaryHex').value = val;
    }

    function addCompanyCustomLink() {
      const container = document.getElementById('companyCustomLinks');
      const div = document.createElement('div');
      div.className = 'custom-link-row';
      div.innerHTML = `
        <input type="text" name="custom_link_name[]" class="input" style="width:35%;" placeholder="Platform Name (e.g. Yelp, Facebook)">
        <input type="url" name="custom_link_url[]" class="input" style="width:55%;" placeholder="https://...">
        <button type="button" class="btn-remove-link" onclick="this.parentElement.remove()">✕</button>
      `;
      container.appendChild(div);
    }

    // Instant Client-Side Image Color Extraction using HTML5 Canvas
    function extractClientColors(input, mode) {
      if (!input.files || !input.files[0]) return;

      const file = input.files[0];
      const reader = new FileReader();

      reader.onload = function(e) {
        if (mode === 'edit') {
          const imgPreview = document.getElementById('activeLogoImg');
          if (imgPreview) imgPreview.src = e.target.result;
        }

        const img = new Image();
        img.crossOrigin = "Anonymous";
        img.onload = function() {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          canvas.width = 100;
          canvas.height = 100;
          ctx.drawImage(img, 0, 0, 100, 100);

          const imageData = ctx.getImageData(0, 0, 100, 100).data;
          const colorCounts = {};

          for (let i = 0; i < imageData.length; i += 16) {
            const r = imageData[i];
            const g = imageData[i+1];
            const b = imageData[i+2];
            const a = imageData[i+3];

            if (a < 128) continue; // Skip transparent background pixels
            if (r > 240 && g > 240 && b > 240) continue; // Skip pure white
            if (r < 15 && g < 15 && b < 15) continue; // Skip pure black

            // Group close colors into buckets
            const qr = Math.round(r / 32) * 32;
            const qg = Math.round(g / 32) * 32;
            const qb = Math.round(b / 32) * 32;
            const hex = "#" + ((1 << 24) + (qr << 16) + (qg << 8) + qb).toString(16).slice(1);

            colorCounts[hex] = (colorCounts[hex] || 0) + 1;
          }

          const sortedHexes = Object.keys(colorCounts).sort((a,b) => colorCounts[b] - colorCounts[a]).slice(0, 6);

          if (sortedHexes.length > 0) {
            const targetRowId = (mode === 'edit') ? 'editSwatchesRow' : 'createSwatchesRow';
            const targetContainerId = (mode === 'edit') ? 'editSwatchesContainer' : 'createSwatchesContainer';
            const row = document.getElementById(targetRowId);
            const container = document.getElementById(targetContainerId);

            if (row) {
              row.innerHTML = '';
              sortedHexes.forEach(hex => {
                const circle = document.createElement('span');
                circle.className = 'swatch-circle';
                circle.style.background = hex;
                circle.onclick = function() {
                  if (mode === 'edit') syncEditPrimary(hex);
                  else syncCreateColor('primary', hex);
                };
                row.appendChild(circle);
              });
              if (container) container.style.display = 'block';

              // Automatically set the top dominant extracted color as primary!
              if (mode === 'edit') syncEditPrimary(sortedHexes[0]);
              else syncCreateColor('primary', sortedHexes[0]);
            }
          }
        };
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  </script>
@endsection
