@extends('layouts.app')
@section('title', 'Activity Reports')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Activity Reports</h1>
    <p class="text-sm text-gray-500 mt-1">Query activity history across any date range.</p>
</div>

{{-- Filter form --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6 no-print">
    <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label for="from" class="block text-xs font-medium text-gray-700 mb-1">From</label>
            <input type="date" id="from" name="from" value="{{ request('from') }}"
                   class="rounded-lg border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] py-1.5 px-3">
        </div>
        <div>
            <label for="to" class="block text-xs font-medium text-gray-700 mb-1">To</label>
            <input type="date" id="to" name="to" value="{{ request('to') }}"
                   class="rounded-lg border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] py-1.5 px-3">
        </div>
        <div>
            <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" class="rounded-lg border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] py-1.5 px-3">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
            </select>
        </div>
        <div>
            <label for="activity_id" class="block text-xs font-medium text-gray-700 mb-1">Activity</label>
            <select id="activity_id" name="activity_id" class="rounded-lg border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] py-1.5 px-3">
                <option value="">All activities</option>
                @foreach($activities as $activity)
                <option value="{{ $activity->id }}" {{ request('activity_id') == $activity->id ? 'selected' : '' }}>
                    {{ $activity->title }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2">
            <button type="submit"
                    class="w-full sm:w-auto px-5 py-2 bg-[#1B6B3A] hover:bg-[#2A8F52] text-white text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm text-center">
                Run Report
            </button>
            @if(request()->hasAny(['from','to','status','activity_id']))
            <a href="{{ route('reports.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
               class="w-full sm:w-auto px-5 py-2 bg-[#12492A] hover:bg-[#1B6B3A] text-white text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
            <a href="{{ route('reports.index', array_merge(request()->query(), ['print' => 'true'])) }}" target="_blank"
               class="w-full sm:w-auto px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print PDF
            </a>
            <button type="button" onclick="openEmailModal()"
                    class="w-full sm:w-auto px-5 py-2 bg-[#F5C518] hover:bg-[#E0B310] text-[#0f1a14] text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Email Report
            </button>
            <a href="{{ route('reports.index') }}" class="w-full sm:w-auto text-center px-3 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Results --}}
@if($logs !== null)
    @if($logs->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
        <p class="font-medium">No records found for the selected criteria.</p>
        <p class="text-sm mt-1">Try expanding the date range or clearing filters.</p>
    </div>
    @else
    
    {{-- Charts Block --}}
    @if(isset($chartData))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="md:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Status Distribution</h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Activity Logs Over Time</h3>
            <div class="h-64">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Status Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartData['status']['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartData['status']['values']) !!},
                        backgroundColor: ['#1B6B3A', '#E63946'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // Timeline Chart
            const ctxTimeline = document.getElementById('timelineChart').getContext('2d');
            new Chart(ctxTimeline, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartData['timeline']['labels']) !!},
                    datasets: [{
                        label: 'Log Entries',
                        data: {!! json_encode($chartData['timeline']['values']) !!},
                        backgroundColor: 'rgba(27, 107, 58, 0.85)',
                        borderColor: '#1B6B3A',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden print-full-width">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between no-print">
            <p class="text-sm text-gray-600">
                @if($logs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                    Showing <span class="font-semibold">{{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold">{{ $logs->total() }}</span> entries
                @else
                    Showing all <span class="font-semibold">{{ $logs->count() }}</span> entries
                @endif
            </p>
        </div>
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Activity</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Updated by</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Remark</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @foreach($logs as $log)
                <tr class="hover:bg-[#F4F7F5] transition-colors duration-100">
                    <td class="px-6 py-3 font-mono text-xs text-gray-600">{{ $log->date->format('d M Y') }}</td>
                    <td class="px-6 py-3">
                        <a href="{{ route('activities.show', $log->activity) }}"
                           class="text-[#1B6B3A] hover:underline font-medium">
                            {{ $log->activity?->title ?? '—' }}
                        </a>
                    </td>
                    <td class="px-6 py-3"><x-status-badge :status="$log->status" /></td>
                    <td class="px-6 py-3">
                        <span class="font-medium text-gray-800">{{ $log->actor_name }}</span>
                        <span class="text-gray-400 text-xs ml-1 capitalize">({{ $log->actor_role }})</span>
                    </td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs truncate">{{ $log->remark ?? '—' }}</td>
                    <td class="px-6 py-3 font-mono text-xs text-gray-400">{{ $log->created_at->format('H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($logs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4 border-t border-gray-100 no-print">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
    @endif
@else
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-400">
    <svg class="w-10 h-10 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    <p class="text-sm font-medium">Select a date range and run a report to see activity history.</p>
</div>
@endif

{{-- Email Customization Modal --}}
<div id="emailModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-40 flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 w-full max-w-lg overflow-hidden transition-all transform">
        <div class="px-6 py-4 bg-[#1B6B3A] text-white flex justify-between items-center">
            <h3 class="font-bold text-lg">Send Report via Email</h3>
            <button type="button" onclick="closeEmailModal()" class="text-white hover:text-green-200 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('reports.email') }}" method="POST" class="p-6 space-y-4">
            @csrf
            {{-- Hidden filter parameters --}}
            <input type="hidden" name="from" value="{{ request('from') }}">
            <input type="hidden" name="to" value="{{ request('to') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="activity_id" value="{{ request('activity_id') }}">

            {{-- Recipients Selector --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Recipients</label>
                <div class="border border-gray-200 rounded-lg p-3 max-h-36 overflow-y-auto space-y-2">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 cursor-pointer">
                        <input type="checkbox" id="selectAllEmails" checked onchange="toggleAllEmails(this)"
                               class="rounded border-gray-300 text-[#1B6B3A] focus:ring-[#1B6B3A]">
                        Select All
                    </label>
                    <hr class="border-gray-100">
                    @foreach($users as $user)
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="recipients[]" value="{{ $user->email }}" checked
                               class="recipient-checkbox rounded border-gray-300 text-[#1B6B3A] focus:ring-[#1B6B3A]">
                        <span>{{ $user->name }} <span class="text-xs text-gray-400">({{ $user->email }})</span></span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Subject --}}
            <div>
                <label for="email_subject" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Subject</label>
                <input type="text" id="email_subject" name="subject" required
                       value="Support Activity Report ({{ request('from') }} to {{ request('to') }})"
                       class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2">
            </div>

            {{-- Custom Message --}}
            <div>
                <label for="email_message" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Custom Message (Optional)</label>
                <textarea id="email_message" name="message" rows="3" placeholder="Enter notes or updates to include at the top of the email..."
                          class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeEmailModal()"
                        class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-[#1B6B3A] hover:bg-[#2A8F52] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Send Email
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEmailModal() {
        document.getElementById('emailModal').classList.remove('hidden');
    }

    function closeEmailModal() {
        document.getElementById('emailModal').classList.add('hidden');
    }

    function toggleAllEmails(master) {
        const checkboxes = document.querySelectorAll('.recipient-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = master.checked;
        });
    }
</script>

@if(request('print') === 'true')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            window.print();
        }, 800);
    });
</script>
@endif
@endsection
