@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Employees — {{ $company->name ?? '' }}</h1>

    <form action="{{ route('admin.employees.store') }}" method="POST" class="flex gap-2 mb-8">
        @csrf
        <input type="text" name="name" placeholder="Employee name" required class="border rounded px-3 py-2 flex-1">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Employee</button>
    </form>

    <div class="grid gap-4">
        @foreach($employees as $employee)
        <div class="border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold">{{ $employee->name }}</span>
                <span class="text-sm text-gray-500">{{ $employee->scans }} scans</span>
            </div>

            {{-- The QR PNG was saved to storage/app/public/qrcodes/{id}.png,
                 which is reachable at /storage/qrcodes/{id}.png once
                 `php artisan storage:link` has been run once during setup. --}}
            <img src="{{ asset('storage/qrcodes/' . $employee->id . '.png') }}" alt="QR code" class="w-32 h-32 mb-3">

            <form action="{{ route('admin.employees.update', $employee) }}" method="POST" class="flex gap-2 mb-2">
                @csrf
                <input type="text" name="name" value="{{ $employee->name }}" class="border rounded px-2 py-1 flex-1">
                <button type="submit" class="text-sm bg-gray-200 px-3 py-1 rounded">Rename</button>
            </form>

            <form action="{{ route('admin.employees.credentials', $employee) }}" method="POST" class="flex gap-2 mb-2">
                @csrf
                <input type="text" name="employee_username" placeholder="Login username" class="border rounded px-2 py-1">
                <input type="password" name="employee_password" placeholder="Login password" class="border rounded px-2 py-1">
                <button type="submit" class="text-sm bg-gray-200 px-3 py-1 rounded">Set Login</button>
            </form>

            <form action="{{ route('admin.employees.delete', $employee) }}" method="POST"
                  onsubmit="return confirm('Delete this employee and all their feedback?')">
                @csrf
                <button type="submit" class="text-sm text-red-600">Delete</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
