@extends('layouts.app')
@section('title', $activity->title)

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('activities.index') }}" class="text-gray-400 hover:text-[#1B6B3A] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900 truncate">{{ $activity->title }}</h1>
        <x-status-badge :status="$activity->is_active ? 'done' : 'pending'" />
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
            <div><dt class="font-medium text-gray-500">Category</dt><dd class="mt-1">{{ $activity->category ?? '—' }}</dd></div>
            <div><dt class="font-medium text-gray-500">Recurrence</dt><dd class="mt-1 capitalize">{{ $activity->recurrence }}</dd></div>
            <div><dt class="font-medium text-gray-500">Created by</dt><dd class="mt-1">{{ $activity->creator?->name ?? '—' }}</dd></div>
            <div><dt class="font-medium text-gray-500">Created</dt><dd class="mt-1 font-mono text-xs">{{ $activity->created_at->format('d M Y H:i') }}</dd></div>
            @if($activity->description)
            <div class="col-span-2">
                <dt class="font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-gray-700">{{ $activity->description }}</dd>
            </div>
            @endif
        </dl>

        @can('update', $activity)
        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-100">
            <a href="{{ route('activities.edit', $activity) }}"
               class="px-4 py-2 border border-[#1B6B3A] text-[#1B6B3A] text-sm font-medium rounded-lg hover:bg-[#1B6B3A] hover:text-white transition-colors duration-150">
                Edit Activity
            </a>
            @can('delete', $activity)
            <form action="{{ route('activities.destroy', $activity) }}" method="POST"
                  onsubmit="return confirm('Delete this activity? This action is recorded in the audit log.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 border border-[#E63946] text-[#E63946] text-sm font-medium rounded-lg hover:bg-[#E63946] hover:text-white transition-colors duration-150">
                    Delete
                </button>
            </form>
            @endcan
        </div>
        @endcan
    </div>

    {{-- 7-Day Completion Trend --}}
    @if(isset($trendData))
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-base font-semibold text-[#1B6B3A] mb-4">7-Day Completion Trend</h2>
        <div class="h-48">
            <canvas id="activityTrendChart"></canvas>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('activityTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($trendData['labels']) !!},
                    datasets: [{
                        label: 'Completion Status',
                        data: {!! json_encode($trendData['values']) !!},
                        backgroundColor: function(context) {
                            const val = context.raw;
                            return val === 100 ? '#1B6B3A' : '#E63946';
                        },
                        borderWidth: 0,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            min: 0,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value === 100 ? 'Done' : (value === 0 ? 'Pending' : '');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw === 100 ? 'Completed' : 'Pending/Not Run';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endif

    {{-- Recent log entries --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-[#1B6B3A] mb-4">Recent Update History</h2>
        @if($activity->logs->isEmpty())
        <p class="text-sm text-gray-400 italic">No updates recorded yet.</p>
        @else
        <div class="space-y-4">
            @foreach($activity->logs as $log)
            <x-activity-timeline-item :log="$log" />
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
