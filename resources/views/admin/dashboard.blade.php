{{-- @extends tells Blade "wrap this page's content inside riyaloerp's
     existing layout file". Adjust 'layouts.app' below to match whatever
     layout riyaloerp already uses (check resources/views/layouts/). --}}
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Review Tracker — {{ $company->name ?? 'No company' }}</h1>

        @if($companies->count() > 1)
        <form action="{{ route('admin.companies.switch') }}" method="POST" class="flex items-center gap-2">
            @csrf
            <select name="company_id" onchange="this.form.submit()" class="border rounded px-2 py-1">
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" @selected($company && $company->id === $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </form>
        @endif
    </div>

    <div class="flex gap-3 mb-6">
        <a href="{{ route('admin.employees') }}" class="text-blue-600 underline">Manage employees</a>
        <a href="{{ route('admin.feedback') }}" class="text-blue-600 underline">Feedback inbox</a>
        <a href="{{ route('admin.companies') }}" class="text-blue-600 underline">Company settings</a>
        <a href="{{ route('admin.export.employees') }}" class="text-blue-600 underline">Export CSV</a>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr class="border-b text-left">
                <th class="py-2">Employee</th>
                <th class="py-2">Total Scans</th>
                <th class="py-2">Good</th>
                <th class="py-2">Okay</th>
                <th class="py-2">Bad</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
            <tr class="border-b">
                <td class="py-2">{{ $employee->name }}</td>
                <td class="py-2">{{ $employee->scans }}</td>
                <td class="py-2 text-green-600">{{ $employee->good_count }}</td>
                <td class="py-2 text-yellow-600">{{ $employee->ok_count }}</td>
                <td class="py-2 text-red-600">{{ $employee->bad_count }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-4 text-center text-gray-500">No employees yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
