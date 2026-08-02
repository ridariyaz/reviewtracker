@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Feedback Inbox</h1>
        <a href="{{ route('admin.export.feedback') }}" class="text-blue-600 underline">Export CSV</a>
    </div>

    <div class="grid gap-4">
        @forelse($feedback as $item)
        <div class="border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold">{{ $item->employee->name ?? 'Unknown' }}</span>
                <span class="text-sm px-2 py-1 rounded
                    @if($item->rating === 'good') bg-green-100 text-green-700
                    @elseif($item->rating === 'ok') bg-yellow-100 text-yellow-700
                    @else bg-red-100 text-red-700 @endif">
                    {{ ucfirst($item->rating) }}
                </span>
            </div>
            @if($item->comment)
                <p class="text-gray-700 mb-2">{{ $item->comment }}</p>
            @endif
            <p class="text-xs text-gray-400 mb-3">{{ $item->created_at }}</p>

            <form action="{{ route('admin.feedback.status', $item) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                    <option value="new" @selected($item->status === 'new')>New</option>
                    <option value="in_progress" @selected($item->status === 'in_progress')>In Progress</option>
                    <option value="resolved" @selected($item->status === 'resolved')>Resolved</option>
                </select>
            </form>
        </div>
        @empty
        <p class="text-gray-500">No feedback yet.</p>
        @endforelse
    </div>
</div>
@endsection
