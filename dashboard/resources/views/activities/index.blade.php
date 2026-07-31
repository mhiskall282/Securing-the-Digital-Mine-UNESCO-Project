@extends('layouts.app')
@section('title', 'All Activities')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Activities</h1>
    @can('create', App\Models\Activity::class)
    <a href="{{ route('activities.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-[#1B6B3A] hover:bg-[#2A8F52]
              text-white text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Activity
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    @if($activities->isEmpty())
    <div class="p-12 text-center text-gray-500">
        <p class="font-medium">No activities yet.</p>
        <p class="text-sm mt-1">Add activities from the Admin panel to start tracking.</p>
    </div>
    @else
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Recurrence</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created by</th>
                <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-50">
            @foreach($activities as $activity)
            <tr class="hover:bg-[#F4F7F5] transition-colors duration-100">
                <td class="px-6 py-4">
                    <a href="{{ route('activities.show', $activity) }}" class="font-medium text-[#1B6B3A] hover:underline">
                        {{ $activity->title }}
                    </a>
                    @if($activity->description)
                    <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $activity->description }}</p>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $activity->category ?? '—' }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $activity->recurrence }}</td>
                <td class="px-6 py-4">
                    @if($activity->is_active)
                    <span class="text-xs bg-green-100 text-green-700 px-2.5 py-0.5 rounded-full font-medium">Active</span>
                    @else
                    <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-0.5 rounded-full font-medium">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $activity->creator?->name ?? '—' }}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <a href="{{ route('activities.show', $activity) }}"
                       class="text-[#1B6B3A] hover:underline font-medium mr-3">View</a>
                    @can('update', $activity)
                    <a href="{{ route('activities.edit', $activity) }}"
                       class="text-gray-600 hover:underline font-medium">Edit</a>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $activities->links() }}
    </div>
    @endif
</div>
@endsection
