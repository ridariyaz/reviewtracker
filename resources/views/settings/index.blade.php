@extends('layouts.app')

@section('title', 'Admin Settings · ReviewTracker')

@section('content')
  <div class="page-header">
    <div>
      <div class="page-title">Admin & System Settings</div>
      <div class="page-subtitle">Manage account security, interface language, and custom additional review links for {{ $brandName }}.</div>
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

  <div style="display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
    <!-- Security & Password Card -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Security</div>
          <div class="card-title">Change Admin Password</div>
        </div>
      </div>
      <form action="{{ route('settings.password') }}" method="POST">
        @csrf
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
        <button class="btn" type="submit" style="margin-top: 8px;">Update Password</button>
      </form>
    </div>

    <!-- Language & Custom Links Card -->
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-kicker">Preferences & Links</div>
          <div class="card-title">Interface Language & Custom Links</div>
        </div>
      </div>
      <form action="{{ route('settings.preferences') }}" method="POST">
        @csrf
        <div class="field">
          <label for="lang_select">System Language</label>
          <select class="input" id="lang_select" name="language">
            @foreach($languages as $code => $lang)
              <option value="{{ $code }}" @selected(($company?->language ?? 'en') === $code)>
                {{ $lang['flag'] }} {{ $lang['name'] }}
              </option>
            @endforeach
          </select>
          <div class="muted" style="margin-top:4px;">Select preferred dashboard & customer interface language.</div>
        </div>

        <hr style="border:none; border-top: 1px solid var(--border-soft); margin: 20px 0;">

        <div style="margin-bottom: 12px;">
          <label style="font-weight: 700;">Custom Additional Review Links</label>
          <div class="muted">Add custom review destinations with user-defined names (e.g. Zomato, Google Maps Branch #2).</div>
        </div>

        <div id="customLinksContainer" style="display: flex; flex-direction: column; gap: 10px;">
          @php $customLinks = $company?->custom_links ?? []; @endphp
          @forelse($customLinks as $index => $link)
            <div class="custom-link-row" style="display: flex; gap: 8px;">
              <input class="input" type="text" name="custom_link_name[]" value="{{ $link['name'] ?? '' }}" placeholder="Link Name (e.g. TripAdvisor)">
              <input class="input" type="url" name="custom_link_url[]" value="{{ $link['url'] ?? '' }}" placeholder="https://...">
            </div>
          @empty
            <div class="custom-link-row" style="display: flex; gap: 8px;">
              <input class="input" type="text" name="custom_link_name[]" placeholder="Link Name (e.g. Yelp)">
              <input class="input" type="url" name="custom_link_url[]" placeholder="https://...">
            </div>
          @endforelse
        </div>

        <button type="button" class="btn btn-secondary" onclick="addCustomLinkRow()" style="font-size: 12px; padding: 6px 12px; margin-top: 10px;">
          + Add Another Custom Link
        </button>

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
      <input class="input" type="text" name="custom_link_name[]" placeholder="Link Name (e.g. Branch Review)">
      <input class="input" type="url" name="custom_link_url[]" placeholder="https://...">
    `;
    container.appendChild(div);
  }
</script>
@endsection
