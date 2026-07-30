@extends('layouts.app')

@section('title', 'Company Settings · ReviewTracker')

@section('styles')
<style>
  .companies-layout { display: grid; gap: 18px; }
  @media (min-width: 900px) {
    .companies-layout { grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr); }
  }
  .companies-layout .input,
  .companies-layout select,
  .companies-layout input[type="file"] {
    border-radius: 10px;
  }
  .companies-layout .btn { border-radius: 10px; }
  .row { display: grid; gap: 10px; }
  @media (min-width: 700px) {
    .row { grid-template-columns: 1fr 1fr; }
  }
  .company-list { list-style: none; padding: 0; margin: 0; }
  .company-item {
    padding: 10px 12px;
    border: 1px solid var(--border-soft);
    border-radius: 12px;
    margin-bottom: 10px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
  }
  .logo-preview {
    width: 96px; height: 96px; border-radius: 999px; overflow: hidden;
    border: 2px solid rgba(148,163,184,0.6);
    box-shadow: 0 8px 25px rgba(0,0,0,0.45);
    background: #020617;
    display: flex; align-items: center; justify-content: center;
  }
  .logo-preview img { width: 100%; height: 100%; object-fit: cover; }
</style>
@endsection

@section('content')
  <div class="page-title">Company settings</div>
  <div class="page-subtitle">Manage your companies, brand kit, and the Google Review URL used for “Good” feedback.</div>

  <div class="companies-layout">
    <div class="card">
      <div class="card-title">Your companies</div>
      <ul class="company-list">
        @forelse($companies as $c)
        <li class="company-item">
          <div>
            <div style="font-weight:600;">{{ $c->name }}</div>
            <div class="muted">ID #{{ $c->id }}</div>
          </div>
          @if($currentCompany && $c->id === $currentCompany->id)
            <span class="pill">Active</span>
          @else
            <form action="{{ route('companies.switch') }}" method="POST" style="margin:0;">
              @csrf
              <input type="hidden" name="company_id" value="{{ $c->id }}">
              <button class="btn" type="submit">Switch</button>
            </form>
          @endif
        </li>
        @empty
        <li class="muted">No companies yet.</li>
        @endforelse
      </ul>

      <hr style="border:none;border-top:1px solid var(--border-soft);margin:16px 0;">

      <div class="card-title">Create new company</div>
      <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="field">
          <label>Company name</label>
          <input class="input" name="name" placeholder="e.g. Acme Dental" required>
        </div>
        <div class="field">
          <label>Logo image (upload) or URL</label>
          <input class="input" type="file" name="logo_file" accept="image/*">
          <input class="input" name="logo_url" placeholder="https://.../logo.png" style="margin-top:8px;">
        </div>
        <div class="row">
          <div class="field">
            <label>Primary color</label>
            <input class="input" name="primary_color" placeholder="#0d6efd" value="#0d6efd">
          </div>
          <div class="field">
            <label>Secondary color</label>
            <input class="input" name="secondary_color" placeholder="#111827" value="#111827">
          </div>
        </div>
        <div class="field">
          <label>Google review URL (optional)</label>
          <input class="input" name="google_review_url" placeholder="https://g.page/.../review">
        </div>
        <button class="btn" type="submit">Create company</button>
      </form>
    </div>

    <div class="card">
      <div class="card-title">Edit active company brand kit</div>
      @if($currentCompany)
      <form action="{{ route('companies.update', $currentCompany) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="field">
          <label>Company name</label>
          <input class="input" name="name" value="{{ $currentCompany->name }}" required>
        </div>
        <div class="field">
          <label>Logo image (upload) or URL</label>
          <input class="input" type="file" name="logo_file" accept="image/*">
          <input class="input" name="logo_url" value="{{ $currentCompany->logo_url }}" placeholder="https://.../logo.png" style="margin-top:8px;">
          <div class="muted" style="margin-top:6px;">Used in admin + customer pages. Previewed below.</div>
        </div>
        <div class="field">
          <label>Customer logo preview</label>
          <div style="display:flex;justify-content:center;margin-top:4px;">
            <div class="logo-preview">
              @if($currentCompany->logo_url)
                <img src="{{ $currentCompany->logo_url }}" alt="{{ $currentCompany->name }} logo">
              @else
                <span class="muted">No logo yet</span>
              @endif
            </div>
          </div>
        </div>
        <div class="row">
          <div class="field">
            <label>Primary color</label>
            <input class="input" name="primary_color" value="{{ $currentCompany->primary_color ?? '#0d6efd' }}" placeholder="#0d6efd">
          </div>
          <div class="field">
            <label>Secondary color</label>
            <input class="input" name="secondary_color" value="{{ $currentCompany->secondary_color ?? '#111827' }}" placeholder="#111827">
          </div>
        </div>
        <div class="field">
          <label>Google Review URL <span style="color:#ef4444;">*</span></label>
          <input class="input" name="google_review_url" value="{{ $currentCompany->google_review_url }}" placeholder="https://g.page/.../review" required>
          <div class="muted" style="margin-top:4px;">Primary destination for positive customer reviews.</div>
        </div>
        <div class="field">
          <label>TripAdvisor Review URL (Optional)</label>
          <input class="input" name="tripadvisor_review_url" value="{{ $currentCompany->tripadvisor_review_url }}" placeholder="https://www.tripadvisor.com/UserReview-...">
        </div>
        <div class="field">
          <label>Yelp Review URL (Optional)</label>
          <input class="input" name="yelp_review_url" value="{{ $currentCompany->yelp_review_url }}" placeholder="https://www.yelp.com/biz/...">
        </div>
        <div class="field">
          <label>Trustpilot Review URL (Optional)</label>
          <input class="input" name="trustpilot_review_url" value="{{ $currentCompany->trustpilot_review_url }}" placeholder="https://www.trustpilot.com/evaluate/...">
          <div class="muted" style="margin-top:4px;">If multiple platform links are added, customers giving a "Great!" rating can post across multiple sites.</div>
        </div>
        <button class="btn" type="submit">Save Brand Kit & Review Links</button>
      </form>
      @else
        <p class="muted">No company found. Create a company first.</p>
      @endif
    </div>
  </div>
@endsection
