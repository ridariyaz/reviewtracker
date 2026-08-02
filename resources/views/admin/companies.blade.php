@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Company Settings</h1>

    @if($currentCompany)
    <form action="{{ route('admin.companies.update', $currentCompany) }}" method="POST"
          enctype="multipart/form-data" class="grid gap-4 mb-10">
        {{-- enctype="multipart/form-data" is required whenever a form
             includes a file upload -- without it, the browser sends the
             file field as plain text instead of the actual file bytes. --}}
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Company Name</label>
            <input type="text" name="name" value="{{ $currentCompany->name }}" required class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Google Review URL</label>
            <input type="url" name="google_review_url" value="{{ $currentCompany->google_review_url }}"
                   placeholder="https://g.page/r/..." class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Logo</label>
            @if($currentCompany->logo_url)
                <img src="{{ $currentCompany->logo_url }}" class="h-16 mb-2">
            @endif
            <input type="file" name="logo_file" accept="image/*" class="block">
            <p class="text-xs text-gray-500 mt-1">Uploading a new logo auto-updates your brand colors below.</p>
        </div>
        <div class="flex gap-4">
            <span class="inline-flex items-center gap-2">
                <span class="w-6 h-6 rounded" style="background: {{ $currentCompany->primary_color }}"></span>
                {{ $currentCompany->primary_color }}
            </span>
            <span class="inline-flex items-center gap-2">
                <span class="w-6 h-6 rounded" style="background: {{ $currentCompany->secondary_color }}"></span>
                {{ $currentCompany->secondary_color }}
            </span>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-fit">Save Changes</button>
    </form>
    @endif

    <h2 class="text-lg font-semibold mb-3">Add Another Company</h2>
    <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-3">
        @csrf
        <input type="text" name="name" placeholder="Company name" required class="border rounded px-3 py-2">
        <input type="url" name="google_review_url" placeholder="Google Review URL" class="border rounded px-3 py-2">
        <input type="file" name="logo_file" accept="image/*">
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded w-fit">Create Company</button>
    </form>
</div>
@endsection
