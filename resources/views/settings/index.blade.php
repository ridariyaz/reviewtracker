@extends('layouts.app')

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
        border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.1));
    }
    .settings-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.6rem;
        font-weight: 700;
    }
    .settings-title svg {
        width: 28px;
        height: 28px;
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
    }
    .settings-card {
        background: var(--card-bg, rgba(15, 23, 42, 0.8));
        border: 1px solid var(--border-color, rgba(255,255,255,0.1));
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        backdrop-filter: blur(12px);
    }
    .card-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 18px;
        color: var(--text-heading, #f8fafc);
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
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--text-main, #e2e8f0);
    }
    .form-control {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid var(--border-color, rgba(255,255,255,0.15));
        background: var(--input-bg, rgba(0, 0, 0, 0.25));
        color: var(--text-main, #f8fafc);
        font-size: 0.95rem;
        transition: border-color 0.2s ease;
    }
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }
    .helper-text {
        font-size: 0.8rem;
        color: #94a3b8;
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
        border: 2px solid rgba(255,255,255,0.2);
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .swatch:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }
    .btn-add-platform {
        background: rgba(59, 130, 246, 0.15);
        border: 1px dashed #3b82f6;
        color: #60a5fa;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .btn-add-platform:hover {
        background: rgba(59, 130, 246, 0.25);
        color: #93c5fd;
    }
    .btn-save {
        background: #3b82f6;
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
        transition: background 0.2s ease;
    }
    .btn-save:hover {
        background: #2563eb;
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
    .btn-danger:hover {
        background: #dc2626;
    }
    .custom-platform-item {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
        align-items: center;
    }
    .theme-toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .theme-toggle-btn {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        padding: 8px 14px;
        border-radius: 20px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 600;
    }
</style>

<div class="settings-container">
    <div class="settings-header">
        <div class="settings-title">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            <span>Company Settings</span>
        </div>
        <div class="theme-toggle-wrapper">
            <button type="button" class="theme-toggle-btn" onclick="toggleThemeMode()">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                <span id="themeLabel">Dark Mode</span>
            </button>
        </div>
    </div>

    @if(session('success_pref'))
        <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
            ✓ {{ session('success_pref') }}
        </div>
    @endif

    @if(session('success_password'))
        <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-weight: 600;">
            ✓ {{ session('success_password') }}
        </div>
    @endif

    <form action="{{ route('settings.company') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Section 1: Company Branding & Logo -->
        <div class="settings-card">
            <div class="card-section-title">
                <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <span>1. Company & Branding</span>
            </div>

            <div class="form-group">
                <label class="form-label">Company Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $company?->name) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Upload Company Logo</label>
                <input type="file" name="logo_file" class="form-control" accept="image/*" onchange="previewLogo(this)">
                <div class="helper-text">Uploading a logo automatically extracts brand color swatches below.</div>
                @if($company?->logo_url)
                    <div style="margin-top: 12px;">
                        <img src="{{ $company->logo_url }}" id="logoPreview" style="max-height: 50px; border-radius: 8px;">
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label">Primary Brand Color</label>
                <input type="color" name="primary_color" id="primaryColorInput" class="form-control" style="height: 48px; padding: 4px;" value="{{ old('primary_color', $company?->primary_color ?? '#0d6efd') }}">
                
                <div class="helper-text">Extracted Logo Color Swatches (Click to set primary color):</div>
                <div class="swatches-row">
                    <div class="swatch" style="background:#0d6efd" onclick="setPrimaryColor('#0d6efd')"></div>
                    <div class="swatch" style="background:#2563eb" onclick="setPrimaryColor('#2563eb')"></div>
                    <div class="swatch" style="background:#16a34a" onclick="setPrimaryColor('#16a34a')"></div>
                    <div class="swatch" style="background:#ea580c" onclick="setPrimaryColor('#ea580c')"></div>
                    <div class="swatch" style="background:#9333ea" onclick="setPrimaryColor('#9333ea')"></div>
                    <div class="swatch" style="background:#0f172a" onclick="setPrimaryColor('#0f172a')"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Secondary Background Color</label>
                <input type="color" name="secondary_color" id="secondaryColorInput" class="form-control" style="height: 48px; padding: 4px;" value="{{ old('secondary_color', $company?->secondary_color ?? '#020617') }}">
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
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                    <input type="checkbox" name="enable_multi_review_prompt" value="1" {{ $company?->enable_multi_review_prompt ? 'checked' : '' }}>
                    <span>Enable Multi-Platform Selection Screen for Customers</span>
                </label>
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

        <form action="{{ route('logout') }}" method="POST" style="border-top: 1px solid var(--border-color, rgba(255,255,255,0.1)); padding-top: 20px;">
            @csrf
            <button type="submit" class="btn-danger">
                <span>Logout of Account</span>
            </button>
        </form>
    </div>
</div>

<script>
    function setPrimaryColor(hex) {
        document.getElementById('primaryColorInput').value = hex;
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

    function toggleThemeMode() {
        const body = document.body;
        const currentTheme = body.getAttribute('data-theme') || 'dark';
        const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';
        body.setAttribute('data-theme', newTheme);
        document.getElementById('themeLabel').textContent = (newTheme === 'dark') ? 'Dark Mode' : 'Light Mode';
        localStorage.setItem('app_theme', newTheme);
    }

    // Initialize theme from localStorage
    const savedTheme = localStorage.getItem('app_theme') || 'dark';
    document.body.setAttribute('data-theme', savedTheme);
    document.getElementById('themeLabel').textContent = (savedTheme === 'dark') ? 'Dark Mode' : 'Light Mode';
</script>
@endsection
