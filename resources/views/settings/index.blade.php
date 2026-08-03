@extends('layouts.app')

@section('title', 'Company Settings & Preferences · ReviewTracker')

@section('content')
<style>
    .settings-container {
        max-width: 900px;
        margin: 0 auto;
        padding-bottom: 60px;
    }
    .settings-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }
    .settings-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-heading);
    }
    .settings-title svg {
        width: 28px;
        height: 28px;
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
    }
    .settings-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }
    .card-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 18px;
        color: var(--text-heading);
    }
    .card-section-title svg {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--text-heading);
    }
    .form-control {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--input-bg);
        color: var(--text-heading);
        font-size: 0.95rem;
        transition: border-color 0.2s ease;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
    }
    .helper-text {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 6px;
        line-height: 1.4;
    }
    .swatches-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .swatch {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.4);
        cursor: pointer;
        transition: transform 0.15s ease;
    }
    .swatch:hover {
        transform: scale(1.15);
    }
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
    }
    .btn-save {
        background: var(--primary);
        color: #ffffff;
        border: none;
        padding: 14px 24px;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-danger {
        background: #ef4444;
        color: #ffffff;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
    }
    .custom-platform-item {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
        align-items: center;
    }
    .companies-list-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    .companies-list-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color);
    }
</style>

<div class="settings-container">
    <div class="settings-header">
        <div class="settings-title">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            <span>Company Settings & Preferences</span>
        </div>
    </div>

    @if(session('success_pref'))
        <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
            ✓ {{ session('success_pref') }}
        </div>
    @endif

    @if(session('success_password'))
        <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
            ✓ {{ session('success_password') }}
        </div>
    @endif

    <!-- Admin Companies Overview Card -->
    <div class="settings-card">
        <div class="card-section-title">
            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <span>Admin Companies Directory</span>
        </div>
        <div class="helper-text" style="margin-bottom:12px;">All companies created under your admin account. Use the switch button or top dropdown to switch active company.</div>
        
        <table class="companies-list-table">
            @isset($companies)
                @foreach($companies as $c)
                <tr>
                    <td>
                        <div style="font-weight:700; color:var(--text-heading);">{{ $c->name }}</div>
                        <div class="muted" style="font-size:11px;">ID #{{ $c->id }} @if($c->google_review_url) · Google URL Set @else · No Link @endif</div>
                    </td>
                    <td style="text-align:right;">
                        @if($company && $c->id === $company->id)
                            <span class="pill">Active Company</span>
                        @else
                            <form action="{{ route('companies.switch') }}" method="POST" style="margin:0; display:inline;">
                                @csrf
                                <input type="hidden" name="company_id" value="{{ $c->id }}">
                                <button type="submit" class="btn" style="padding:6px 14px; font-size:12px; background:var(--input-bg); color:var(--text-heading); border:1px solid var(--border-color);">Switch</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            @endisset
        </table>
    </div>

    <!-- Main Panel: Editing Active Company -->
    <form action="{{ route('settings.company') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Section 1: Active Company Branding & Logo -->
        <div class="settings-card">
            <div class="card-section-title">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                <span>1. Edit Active Company: {{ $company?->name }}</span>
            </div>

            <div class="form-group">
                <label class="form-label">Company Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $company?->name) }}" required>
                <div class="helper-text">Official business name rendered on customer QR landing pages and printable standees.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Upload Company Logo</label>
                <input type="file" name="logo_file" class="form-control" accept="image/*" onchange="extractSettingsLogoColors(this)">
                <div class="helper-text">Selecting a logo file instantly extracts brand color swatches below!</div>
                @if($company?->logo_url)
                    <div style="margin-top: 12px;">
                        <img src="{{ $company->logo_url }}" id="settingsLogoPreview" style="max-height: 50px; border-radius: 8px;">
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label">Primary Brand Color (Color Wheel, Hex Text, or Extracted Swatches)</label>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="color" id="settingsPrimaryWheel" style="width:44px; height:44px; padding:2px; border-radius:10px; border:1px solid var(--border-color); cursor:pointer;" value="{{ old('primary_color', $company?->primary_color ?? '#0d6efd') }}" onchange="syncSettingsPrimary(this.value)">
                    <input type="text" name="primary_color" id="settingsPrimaryHex" class="form-control" value="{{ old('primary_color', $company?->primary_color ?? '#0d6efd') }}" placeholder="#0d6efd" oninput="syncSettingsPrimary(this.value)">
                </div>
                
                <div class="helper-text" style="font-weight:700; color:var(--text-heading); margin-top:10px;">Extracted Logo Colors (Click any circle to set primary color):</div>
                <div class="swatches-row" id="settingsSwatchesRow">
                    <div class="swatch" style="background:#0d6efd" onclick="syncSettingsPrimary('#0d6efd')"></div>
                    <div class="swatch" style="background:#2563eb" onclick="syncSettingsPrimary('#2563eb')"></div>
                    <div class="swatch" style="background:#16a34a" onclick="syncSettingsPrimary('#16a34a')"></div>
                    <div class="swatch" style="background:#ea580c" onclick="syncSettingsPrimary('#ea580c')"></div>
                    <div class="swatch" style="background:#9333ea" onclick="syncSettingsPrimary('#9333ea')"></div>
                    <div class="swatch" style="background:#0f172a" onclick="syncSettingsPrimary('#0f172a')"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Secondary Background Color</label>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="color" id="settingsSecondaryWheel" style="width:44px; height:44px; padding:2px; border-radius:10px; border:1px solid var(--border-color); cursor:pointer;" value="{{ old('secondary_color', $company?->secondary_color ?? '#111827') }}" onchange="syncSettingsSecondary(this.value)">
                    <input type="text" name="secondary_color" id="settingsSecondaryHex" class="form-control" value="{{ old('secondary_color', $company?->secondary_color ?? '#111827') }}" placeholder="#111827" oninput="syncSettingsSecondary(this.value)">
                </div>
                <div class="helper-text">Background accent color for customer review cards and printable standees.</div>
            </div>
        </div>

        <!-- Section 2: Review Platforms & Custom Links -->
        <div class="settings-card">
            <div class="card-section-title">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                <span>2. Review Platforms & Links</span>
            </div>

            <div class="form-group">
                <label class="form-label">Main Google Review URL</label>
                <input type="url" name="google_review_url" class="form-control" placeholder="https://g.page/r/your-google-place-id/review" value="{{ old('google_review_url', $company?->google_review_url) }}">
                <div class="helper-text">Google Reviews is your default public destination. If left blank, customers will see the "No review link set" notice page.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Additional Review Platforms (Click + Add to configure custom sites)</label>
                <div id="customLinksContainer">
                    @if(is_array($company?->custom_links))
                        @foreach($company->custom_links as $link)
                            <div class="custom-platform-item">
                                <input type="text" name="custom_link_name[]" class="form-control" style="width: 35%;" value="{{ $link['name'] ?? '' }}" placeholder="Platform Name (e.g. Trustpilot)">
                                <input type="url" name="custom_link_url[]" class="form-control" style="width: 55%;" value="{{ $link['url'] ?? '' }}" placeholder="https://...">
                                <button type="button" class="btn-danger" style="padding: 10px 14px;" onclick="this.parentElement.remove()">✕</button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" class="btn-add-platform" onclick="addCustomPlatform()">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>+ Add Review Link</span>
                </button>
                <div class="helper-text">Add any custom platform (Yelp, TripAdvisor, Trustpilot, Facebook, or your own site link).</div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem; color: var(--text-heading);">
                    <input type="checkbox" name="enable_multi_review_prompt" value="1" {{ $company?->enable_multi_review_prompt ? 'checked' : '' }}>
                    <span>Enable Multi-Platform Selection Screen for Customers</span>
                </label>
                <div class="helper-text">When checked, customers giving a positive rating see choices for Google and custom links.</div>
            </div>
        </div>

        <!-- Section 3: Review Personalization (Industry & Custom Keywords) -->
        <div class="settings-card">
            <div class="card-section-title">
                <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                <span>3. Review Personalization & Industry Field</span>
            </div>

            <div class="form-group">
                <label class="form-label">Business Industry / Field</label>
                <select name="industry" class="form-control">
                    <option value="">Select Industry...</option>
                    @foreach($industries as $ind)
                        <option value="{{ $ind }}" {{ ($company?->industry === $ind) ? 'selected' : '' }}>{{ $ind }}</option>
                    @endforeach
                </select>
                <div class="helper-text">Helps the review generator tailor sentences specifically for your business type.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Custom Keywords for Autogenerated Reviews</label>
                <textarea name="keywords" class="form-control" rows="3" placeholder="e.g. fast service, fresh espresso, knowledgeable team, clean store, fair pricing">{{ old('keywords', $company?->keywords) }}</textarea>
                <div class="helper-text">Enter key phrases separated by commas. These phrases will be dynamically woven into generated human reviews.</div>
            </div>
        </div>

        <!-- Section 3.5: Customer Gamification & Reward Lottery -->
        <div class="settings-card">
            <div class="card-section-title">
                <svg viewBox="0 0 24 24"><path d="M20 12v10H4V12"></path><path d="M22 7H2v5h20V7z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
                <span>3.5 Customer Review Gamification & Reward Lottery</span>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.95rem; font-weight: 600; color: var(--text-heading);">
                    <input type="checkbox" name="enable_gamification" value="1" {{ $company?->enable_gamification ? 'checked' : '' }}>
                    <span>Enable Customer Lucky Winner Contest</span>
                </label>
                <div class="helper-text">When enabled, customers see a gamified reward contest banner, and qualifying reviewers win a reward gift.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Winner Selection Method</label>
                <div style="display:flex; flex-direction:column; gap:10px; margin-top:6px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.9rem; color:var(--text-heading);">
                        <input type="radio" name="gamification_mode" value="random" {{ ($company?->gamification_mode ?? 'random') === 'random' ? 'checked' : '' }}>
                        <span><strong>Random Auto-Picker:</strong> Automatically pick every Nth customer scan as a winner.</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.9rem; color:var(--text-heading);">
                        <input type="radio" name="gamification_mode" value="employee" {{ ($company?->gamification_mode) === 'employee' ? 'checked' : '' }}>
                        <span><strong>Staff Choice Only:</strong> Staff click "Make Next Scan Win" button right before showing QR to customer.</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Random Winner Interval Threshold (for Random Auto-Picker)</label>
                <input type="number" name="gamification_interval" class="form-control" value="{{ old('gamification_interval', $company?->gamification_interval ?? 50) }}" min="1" max="100000">
                <div class="helper-text">Example: Entering 50 means every 50th customer scan/review wins a prize! (Tip: Set to 1 for live testing).</div>
            </div>

            <div class="form-group">
                <label class="form-label">Reward / Gift Description</label>
                <input type="text" name="gamification_reward" class="form-control" value="{{ old('gamification_reward', $company?->gamification_reward ?? 'Free Coffee / Gift Voucher') }}" placeholder="e.g. Free Coffee, 20% Off Voucher, Free Dessert">
                <div class="helper-text">The gift description rendered on the Lucky Winner claim modal.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Upload Prize Photo / Gift Coupon Image</label>
                <input type="file" name="gamification_image_file" class="form-control" accept="image/*">
                <div class="helper-text">Upload a picture of the gift voucher or prize coupon shown on the winner popup card.</div>
                @if($company?->gamification_image_url)
                    <div style="margin-top: 10px;">
                        <img src="{{ $company->gamification_image_url }}" style="max-height: 90px; border-radius: 8px; border: 1px solid var(--border-color);">
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 4: Universal Default Language Settings -->
        <div class="settings-card">
            <div class="card-section-title">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                <span>4. Universal Default Language</span>
            </div>

            <div class="form-group">
                <label class="form-label">Default Customer Language</label>
                <select name="language" class="form-control">
                    @foreach($languages as $code => $lang)
                        <option value="{{ $code }}" {{ ($company?->language === $code) ? 'selected' : '' }}>
                            {{ $lang['flag'] }} {{ $lang['name'] }}
                        </option>
                    @endforeach
                </select>
                <div class="helper-text">Sets the default language for customer QR scans. Employees can also select individual languages on their dashboard.</div>
            </div>
        </div>

        <div style="margin-bottom: 32px;">
            <button type="submit" class="btn-save">
                <svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                <span>Save All Company Settings</span>
            </button>
        </div>
    </form>

    <!-- Section 5: Password Change & Account Controls (Cleanly at Bottom) -->
    <div class="settings-card">
        <div class="card-section-title">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            <span>5. Account Password & Logout</span>
        </div>

        <form action="{{ route('settings.password') }}" method="POST" style="margin-bottom: 24px;">
            @csrf
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn-save" style="background:#475569;">Change Password</button>
        </form>

        <form action="{{ route('logout') }}" method="POST" style="border-top: 1px solid var(--border-color); padding-top: 20px;">
            @csrf
            <button type="submit" class="btn-danger">
                <span>Logout of Account</span>
            </button>
        </form>
    </div>
</div>

<script>
    function syncSettingsPrimary(hex) {
        document.getElementById('settingsPrimaryWheel').value = hex;
        document.getElementById('settingsPrimaryHex').value = hex;
    }
    function syncSettingsSecondary(hex) {
        document.getElementById('settingsSecondaryWheel').value = hex;
        document.getElementById('settingsSecondaryHex').value = hex;
    }

    function addCustomPlatform() {
        const container = document.getElementById('customLinksContainer');
        const div = document.createElement('div');
        div.className = 'custom-platform-item';
        div.innerHTML = `
            <input type="text" name="custom_link_name[]" class="form-control" style="width: 35%;" placeholder="Platform Name (e.g. Yelp, Facebook)">
            <input type="url" name="custom_link_url[]" class="form-control" style="width: 55%;" placeholder="https://...">
            <button type="button" class="btn-danger" style="padding: 10px 14px;" onclick="this.parentElement.remove()">✕</button>
        `;
        container.appendChild(div);
    }

    function extractSettingsLogoColors(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const preview = document.getElementById('settingsLogoPreview');
            if (preview) preview.src = e.target.result;

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

                    if (a < 128) continue;
                    if (r > 240 && g > 240 && b > 240) continue;
                    if (r < 15 && g < 15 && b < 15) continue;

                    const qr = Math.round(r / 32) * 32;
                    const qg = Math.round(g / 32) * 32;
                    const qb = Math.round(b / 32) * 32;
                    const hex = "#" + ((1 << 24) + (qr << 16) + (qg << 8) + qb).toString(16).slice(1);
                    colorCounts[hex] = (colorCounts[hex] || 0) + 1;
                }

                const sortedHexes = Object.keys(colorCounts).sort((a,b) => colorCounts[b] - colorCounts[a]).slice(0, 6);

                if (sortedHexes.length > 0) {
                    const row = document.getElementById('settingsSwatchesRow');
                    if (row) {
                        row.innerHTML = '';
                        sortedHexes.forEach(hex => {
                            const swatch = document.createElement('div');
                            swatch.className = 'swatch';
                            swatch.style.background = hex;
                            swatch.onclick = function() { syncSettingsPrimary(hex); };
                            row.appendChild(swatch);
                        });
                        syncSettingsPrimary(sortedHexes[0]);
                    }
                }
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
