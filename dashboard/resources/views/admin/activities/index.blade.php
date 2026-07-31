@extends('layouts.app')
@section('title', 'Manage Activities')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Manage Activities</h1>
    <a href="{{ route('admin.activities.create') }}" class="px-4 py-2 bg-[#1B6B3A] hover:bg-[#2A8F52] text-white text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm">
        New Activity
    </a>
</div>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($activities as $activity)
            <tr class="{{ $activity->trashed() ? 'opacity-50 bg-gray-50' : 'hover:bg-[#F4F7F5]' }} transition-colors duration-100">
                <td class="px-6 py-3 font-medium text-gray-900">{{ $activity->title }}</td>
                <td class="px-6 py-3 text-gray-500">{{ $activity->category ?? '—' }}</td>
                <td class="px-6 py-3">
                    @if($activity->trashed())
                        <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Deleted</span>
                    @elseif($activity->is_active)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
                    @else
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-3 text-right">
                    @unless($activity->trashed())
                    <a href="{{ route('admin.activities.edit', $activity) }}" class="text-[#1B6B3A] hover:underline font-medium mr-3">Edit</a>
                    <form action="{{ route('admin.activities.destroy', $activity) }}" method="POST" class="inline"
                          onsubmit="return confirm('Delete this activity?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-[#E63946] hover:underline font-medium">Delete</button>
                    </form>
                    @endunless
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">{{ $activities->links() }}</div>
</div>
@endsection
