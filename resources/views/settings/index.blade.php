@extends('layouts.app')

@section('title', 'Admin Settings · ReviewTracker')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Admin & System Settings</div>
      <div class="page-subtitle">Manage security, notification email, customer language, and multi-review options for {{ $brandName }}.</div>
    </div>
  </div>

  @if(session('success_password') || session('success_pref'))
    <div style="padding: 12px 18px; border-radius: 12px; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-weight: 600; margin-bottom: 20px;">
      ✓ {{ session('success_password') ?: session('success_pref') }}
    </div>
  @endif

  @if($errors->any())
    <div style="padding: 12px 18px; border-radius: 12px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 600; margin-bottom: 20px;">
      ⚠️ {{ $errors->first() }}
    </div>
  @endif

  <div style="display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));">
    <!-- Security & Notifications Card -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Security & Notifications</div>
          <div class="card-title">Admin Password & Alert Email</div>
        </div>
      </div>

      <form action="{{ route('settings.password') }}" method="POST" style="margin-bottom: 24px;">
        @csrf
        <div style="font-weight:700; margin-bottom:8px; font-size:14px;">Change Admin Password</div>
        <div class="field">
          <label for="curr_pass">Current Password <span style="color:#ef4444;">*</span></label>
          <input class="input" type="password" id="curr_pass" name="current_password" required>
        </div>
        <div class="field">
          <label for="new_pass">New Password (min 8 chars) <span style="color:#ef4444;">*</span></label>
          <input class="input" type="password" id="new_pass" name="new_password" required>
        </div>
        <div class="field">
          <label for="conf_pass">Confirm New Password <span style="color:#ef4444;">*</span></label>
          <input class="input" type="password" id="conf_pass" name="new_password_confirmation" required>
        </div>
        <button class="btn" type="submit">Update Password</button>
      </form>
    </div>

    <!-- Customer Language & Multi-Review Settings Card -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Preferences & Destinations</div>
          <div class="card-title">Language & Multi-Platform Routing</div>
        </div>
      </div>
      <form action="{{ route('settings.preferences') }}" method="POST">
        @csrf
        
        <div class="field">
          <label for="notif_email">Alert Notification Email</label>
          <input class="input" type="email" id="notif_email" name="notification_email" value="{{ $company?->notification_email ?: $user->email }}" placeholder="manager@example.com">
          <div class="muted" style="margin-top:4px;">Unresponded bad feedback alerts will be sent to this email address.</div>
        </div>

        <div class="field" style="margin-top:16px;">
          <label for="lang_select">System & Customer Language</label>
          <select class="input" id="lang_select" name="language">
            @foreach($languages as $code => $lang)
              <option value="{{ $code }}" @selected(($company?->language ?? 'en') === $code)>
                {{ $lang['flag'] }} {{ $lang['name'] }}
              </option>
            @endforeach
          </select>
          <div class="muted" style="margin-top:4px;">Translates customer QR screens into English, Malayalam, Hindi, or Arabic (with RTL).</div>
        </div>

        <hr style="border:none; border-top: 1px solid var(--border-soft); margin: 20px 0;">

        <!-- Toggle Switch for Multi-Review Prompt -->
        <div style="background:#f8fafc; padding:14px 16px; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:16px;">
          <label style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; font-weight:700; color:#0f172a; margin:0;">
            <span>Enable Multi-Platform Review Selector</span>
            <input type="checkbox" name="enable_multi_review_prompt" value="1" @checked($company?->enable_multi_review_prompt) style="width:20px; height:20px; cursor:pointer;">
          </label>
          <div class="muted" style="margin-top:6px; font-size:12px;">
            By default (OFF), positive customer ratings redirect <strong>directly to Google Reviews</strong> with zero intermediate steps.
          </div>
        </div>

        <div style="margin-bottom: 10px;">
          <label style="font-weight: 700;">Custom Additional Review Links</label>
          <div class="muted" style="font-size:12px;">Click a preset to quickly add a popular review platform:</div>
          
          <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:8px;">
            <button type="button" class="btn btn-secondary" style="font-size:11px; padding:4px 10px;" onclick="addPresetLink('TripAdvisor', 'https://www.tripadvisor.com/')">+ TripAdvisor</button>
            <button type="button" class="btn btn-secondary" style="font-size:11px; padding:4px 10px;" onclick="addPresetLink('Yelp', 'https://www.yelp.com/')">+ Yelp</button>
            <button type="button" class="btn btn-secondary" style="font-size:11px; padding:4px 10px;" onclick="addPresetLink('Trustpilot', 'https://www.trustpilot.com/')">+ Trustpilot</button>
            <button type="button" class="btn btn-secondary" style="font-size:11px; padding:4px 10px;" onclick="addPresetLink('Facebook', 'https://www.facebook.com/')">+ Facebook</button>
          </div>
        </div>

        <div id="customLinksContainer" style="display: flex; flex-direction: column; gap: 10px; margin-top:12px;">
          @php $customLinks = $company?->custom_links ?? []; @endphp
          @forelse($customLinks as $index => $link)
            <div class="custom-link-row" style="display: flex; gap: 8px;">
              <input class="input" type="text" name="custom_link_name[]" value="{{ $link['name'] ?? '' }}" placeholder="Link Name (e.g. TripAdvisor)">
              <input class="input" type="url" name="custom_link_url[]" value="{{ $link['url'] ?? '' }}" placeholder="https://...">
            </div>
          @empty
            <div class="custom-link-row" style="display: flex; gap: 8px;">
              <input class="input" type="text" name="custom_link_name[]" placeholder="Link Name (e.g. TripAdvisor)">
              <input class="input" type="url" name="custom_link_url[]" placeholder="https://...">
            </div>
          @endforelse
        </div>

        <button type="button" class="btn btn-secondary" onclick="addCustomLinkRow()" style="font-size: 12px; padding: 6px 12px; margin-top: 10px;">
          + Add Blank Link
        </button>

        <!-- Warning Notice at Bottom -->
        <div style="margin-top:20px; padding:12px 14px; border-radius:12px; background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; font-size:12px; line-height:1.4;">
          ⚠️ <strong>Note on Conversion Rates:</strong> Enabling multiple review links requires customers to choose a destination before writing a review, which may lower your overall Google Review completion rate.
        </div>

        <div style="margin-top: 20px;">
          <button class="btn" type="submit">Save Preferences & Links</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  function addCustomLinkRow() {
    const container = document.getElementById('customLinksContainer');
    const div = document.createElement('div');
    div.className = 'custom-link-row';
    div.style.cssText = 'display: flex; gap: 8px;';
    div.innerHTML = `
      <input class="input" type="text" name="custom_link_name[]" placeholder="Link Name (e.g. Custom Site)">
      <input class="input" type="url" name="custom_link_url[]" placeholder="https://...">
    `;
    container.appendChild(div);
  }

  function addPresetLink(name, defaultUrl) {
    const container = document.getElementById('customLinksContainer');
    const div = document.createElement('div');
    div.className = 'custom-link-row';
    div.style.cssText = 'display: flex; gap: 8px;';
    div.innerHTML = `
      <input class="input" type="text" name="custom_link_name[]" value="${name}" placeholder="Link Name">
      <input class="input" type="url" name="custom_link_url[]" value="${defaultUrl}" placeholder="https://...">
    `;
    container.appendChild(div);
  }
</script>
@endsection
