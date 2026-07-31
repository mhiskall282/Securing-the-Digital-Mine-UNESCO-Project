@extends('layouts.app')
@section('title', 'SRE Monitoring Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                SRE Monitoring Dashboard
            </h1>
            <p class="text-sm text-gray-500 mt-1">Live system overview — auto-refreshes every 60 seconds</p>
        </div>
        <div class="flex items-center gap-3 text-xs text-gray-400 font-mono">
            <span>Last updated: <strong class="text-gray-700">{{ now()->format('H:i:s') }}</strong></span>
            <a href="{{ route('monitoring.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg font-semibold text-gray-600 transition-colors">↻ Refresh</a>
        </div>
    </div>

    {{-- ── System Health Metrics ────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <p class="text-3xl font-black text-gray-900">{{ $totalActivities }}</p>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-1">Total Checks</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <p class="text-3xl font-black text-gray-900">{{ $totalUsers }}</p>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-1">Team Members</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col items-center text-center">
            <p class="text-3xl font-black text-gray-900">{{ $todayLogs }}</p>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-1">Events Today</p>
        </div>
        <div class="bg-amber-50 rounded-xl border border-amber-200 shadow-sm p-4 flex flex-col items-center text-center">
            <p class="text-3xl font-black text-amber-600">{{ $pendingToday }}</p>
            <p class="text-xs font-semibold text-amber-500 uppercase tracking-wider mt-1">Pending Today</p>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-200 shadow-sm p-4 flex flex-col items-center text-center">
            <p class="text-3xl font-black text-[#1B6B3A]">{{ $doneToday }}</p>
            <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mt-1">Done Today</p>
        </div>
    </div>

    {{-- ── Stale Pending Alerts ─────────────────────────────────────────── --}}
    @if($staleAlerts->isNotEmpty())
    <div class="bg-red-50 rounded-xl border border-red-200 p-5">
        <h2 class="text-sm font-bold text-red-700 flex items-center gap-2 mb-3">
            <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            STALE ALERTS — {{ $staleAlerts->count() }} activities remained Pending after shift end
        </h2>
        <div class="space-y-2">
            @foreach($staleAlerts as $alert)
            <div class="bg-white rounded-lg border border-red-100 px-4 py-2.5 flex items-center justify-between text-sm">
                <div>
                    <span class="font-semibold text-gray-800">{{ $alert->activity?->title ?? 'Unknown' }}</span>
                    <span class="text-gray-400 ml-2 text-xs">by {{ $alert->actor_name }}</span>
                </div>
                <span class="text-xs font-mono text-red-500 bg-red-50 px-2 py-0.5 rounded-full border border-red-100">
                    {{ $alert->date->format('d M Y') }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Charts Row ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- 7-Day Trend Bar Chart --}}
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#1B6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                7-Day Completion Trend
            </h2>
            <canvas id="trendChart" height="100"></canvas>
        </div>

        {{-- Event Type Doughnut --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#F5C518]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Audit Event Types
            </h2>
            <canvas id="eventChart" height="160"></canvas>
            <div class="mt-3 space-y-1">
                @foreach($eventBreakdown as $evt)
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <span class="capitalize">{{ str_replace('_', ' ', $evt->event) }}</span>
                    <span class="font-bold text-gray-900">{{ $evt->count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Category Breakdown + Top Contributors ───────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Category Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-bold text-gray-900 mb-4">Category Completion (Last 30 Days)</h2>
            <div class="space-y-3">
                @forelse($categoryBreakdown as $cat => $stats)
                @php $pct = $stats['total'] > 0 ? round($stats['done'] / $stats['total'] * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-semibold text-gray-700">{{ $cat ?: 'Uncategorised' }}</span>
                        <span class="text-gray-500">{{ $stats['done'] }}/{{ $stats['total'] }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $pct >= 80 ? 'bg-[#1B6B3A]' : ($pct >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No data yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Top Contributors --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-sm font-bold text-gray-900 mb-4">Top Contributors (Last 7 Days)</h2>
            <div class="space-y-3">
                @forelse($topContributors as $idx => $contributor)
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-green-50 text-[#1B6B3A] text-xs font-bold flex items-center justify-center flex-shrink-0">
                        {{ $idx + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $contributor->actor_name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ $contributor->actor_role }}</p>
                    </div>
                    <span class="text-sm font-bold text-gray-900 flex-shrink-0">{{ $contributor->log_count }} logs</span>
                </div>
                @empty
                <p class="text-sm text-gray-400">No activity logged this week.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Live Audit Log Stream ────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-900 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                </span>
                <h2 class="text-sm font-bold text-white font-mono">AUDIT LOG STREAM</h2>
            </div>
            <span class="text-xs text-gray-400 font-mono">{{ $auditLogs->total() }} total entries</span>
        </div>
        <div class="font-mono text-xs overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold uppercase tracking-wider">Timestamp</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold uppercase tracking-wider">Actor</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold uppercase tracking-wider">Role</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold uppercase tracking-wider">Event</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold uppercase tracking-wider">Subject</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold uppercase tracking-wider">IP</th>
                        <th class="px-4 py-2 text-left text-gray-500 font-semibold uppercase tracking-wider">Diff</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($auditLogs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="px-4 py-2.5 font-semibold text-gray-800 whitespace-nowrap">{{ $log->actor_name }}</td>
                        <td class="px-4 py-2.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                {{ $log->actor_role === 'admin' ? 'bg-red-100 text-red-700' : ($log->actor_role === 'lead' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $log->actor_role ?? 'system' }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                {{ str_contains($log->event, 'delete') ? 'bg-red-100 text-red-700' : (str_contains($log->event, 'create') ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ str_replace('_', ' ', $log->event) }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-gray-600">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $log->actor_ip }}</td>
                        <td class="px-4 py-2.5">
                            @if($log->old_values || $log->new_values)
                            <button onclick="toggleDiff({{ $log->id }})"
                                    class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded text-[10px] font-bold transition-colors">
                                VIEW
                            </button>
                            <div id="diff-{{ $log->id }}" class="hidden mt-2 p-2 bg-gray-900 text-green-300 rounded text-[10px] max-w-xs overflow-x-auto">
                                @if($log->old_values)
                                <div class="text-red-400">- {{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</div>
                                @endif
                                @if($log->new_values)
                                <div class="text-green-400">+ {{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</div>
                                @endif
                            </div>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400 text-sm">No audit entries yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($auditLogs->hasPages())
        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
            {{ $auditLogs->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
    const trendData = @json($trendChart);

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels: trendData.labels,
            datasets: [
                {
                    label: 'Done',
                    data: trendData.done,
                    backgroundColor: 'rgba(27,107,58,0.8)',
                    borderRadius: 4,
                },
                {
                    label: 'Pending',
                    data: trendData.pending,
                    backgroundColor: 'rgba(245,197,24,0.8)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                x: { stacked: true, grid: { display: false } },
                y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    const evtData = @json($eventBreakdown->pluck('count'));
    const evtLabels = @json($eventBreakdown->pluck('event')->map(fn($e) => str_replace('_', ' ', $e)));
    const evtColors = ['#1B6B3A','#F5C518','#E63946','#2A8F52','#6B7280','#0EA5E9','#8B5CF6'];

    new Chart(document.getElementById('eventChart'), {
        type: 'doughnut',
        data: {
            labels: evtLabels,
            datasets: [{ data: evtData, backgroundColor: evtColors, borderWidth: 0 }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    function toggleDiff(id) {
        const el = document.getElementById('diff-' + id);
        el.classList.toggle('hidden');
    }
</script>

{{-- Auto-refresh every 60 seconds --}}
<script>
    setTimeout(() => location.reload(), 60000);
</script>
@endsection
