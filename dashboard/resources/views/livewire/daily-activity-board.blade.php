<div>
    {{-- ── Date picker header ──────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daily Activity Board</h1>
            <p class="text-sm text-gray-500 mt-0.5">Shift handover view — {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <label for="board-date" class="text-sm font-medium text-gray-700">Date:</label>
            <input type="date"
                   id="board-date"
                   wire:model.live="date"
                   max="{{ today()->format('Y-m-d') }}"
                   class="block rounded-md border-gray-300 shadow-sm text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] py-1.5 px-3">
            @if($date === today()->format('Y-m-d'))
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F5C518] text-gray-900">
                    LIVE
                </span>
            @endif
        </div>
    </div>

    {{-- ── Role-based Welcome & Console ────────────────────────── --}}
    <div class="grid grid-cols-1 gap-6 mb-6">
        @if(auth()->user()->isAdmin())
        <div class="bg-gradient-to-br from-[#12492A] to-[#1B6B3A] rounded-xl shadow-sm p-6 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="bg-[#F5C518] text-gray-900 text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Administrator Console</span>
                <h2 class="text-xl font-bold mt-2">Welcome back, {{ auth()->user()->name }}</h2>
                <p class="text-green-100 text-sm mt-1">Manage system configurations, user access, and oversee SRE shift operations.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.activities.index') }}" class="px-4 py-2 bg-white text-[#1B6B3A] hover:bg-green-50 text-sm font-bold rounded-lg transition-colors">
                    Manage Activities
                </a>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-[#F5C518] text-gray-900 hover:bg-[#E0B310] text-sm font-bold rounded-lg transition-colors">
                    Manage Users
                </a>
            </div>
        </div>
        @elseif(auth()->user()->isLead())
        <div class="bg-gradient-to-br from-[#12492A] to-[#1B6B3A] rounded-xl shadow-sm p-6 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="bg-emerald-400 text-gray-900 text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Team Lead Dashboard</span>
                <h2 class="text-xl font-bold mt-2">Welcome back, {{ auth()->user()->name }}</h2>
                <p class="text-green-100 text-sm mt-1">Review shift handovers, manage tasks, and generate performance reports.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.activities.index') }}" class="px-4 py-2 bg-white text-[#1B6B3A] hover:bg-green-50 text-sm font-bold rounded-lg transition-colors">
                    Create/Edit Checks
                </a>
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-[#F5C518] text-gray-900 hover:bg-[#E0B310] text-sm font-bold rounded-lg transition-colors">
                    System Reports
                </a>
            </div>
        </div>
        @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <span class="bg-slate-100 text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-full uppercase tracking-wider">Operator Portal</span>
                <h2 class="text-xl font-bold text-gray-900 mt-2">Hello, {{ auth()->user()->name }}</h2>
                <p class="text-gray-500 text-sm mt-1">You are logged in as a Support Operator. Review and complete shift checkoff duties below.</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Current Designation</p>
                <p class="text-sm font-bold text-gray-800">{{ auth()->user()->designation ?? 'Support Operator' }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Stats bar ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $pending->count() + $done->count() }}</p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Total Activities</p>
        </div>
        <div class="bg-amber-50 rounded-lg shadow-sm p-4 border border-[#F5C518] border-opacity-50 text-center">
            <p class="text-3xl font-bold text-[#E63946]">{{ $pending->count() }}</p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Pending</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border border-[#1B6B3A] border-opacity-30 text-center">
            <p class="text-3xl font-bold text-[#1B6B3A]">{{ $done->count() }}</p>
            <p class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Done</p>
        </div>
    </div>

    @php
        $totalCount = $pending->count() + $done->count();
        $doneCount = $done->count();
        $completionRate = $totalCount > 0 ? round(($doneCount / $totalCount) * 100) : 0;
    @endphp
    @if($totalCount > 0)
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
        <div class="flex items-center justify-between text-xs font-semibold text-gray-700 mb-2">
            <span class="uppercase tracking-wider">Completion Progress</span>
            <span class="text-[#1B6B3A] font-bold">{{ $completionRate }}% ({{ $doneCount }} of {{ $totalCount }} completed)</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3.5 overflow-hidden">
            <div class="bg-gradient-to-r from-[#1B6B3A] to-[#2A8F52] h-full rounded-full transition-all duration-500 ease-out"
                 style="width: {{ $completionRate }}%"></div>
        </div>
    </div>
    @endif

    @if($pending->isEmpty() && $done->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-12 text-center border border-gray-100">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 font-medium">No activities found for this date.</p>
            <p class="text-gray-400 text-sm mt-1">Activities created in Admin will appear here.</p>
        </div>
    @else

    {{-- ── PENDING SECTION ─────────────────────────────────────── --}}
    @if($pending->isNotEmpty())
    <section class="mb-8">
        {{-- Angled section header (Npontu geometric motif) --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-[#E63946]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <h2 class="text-sm font-bold text-[#E63946] uppercase tracking-widest">Needs Attention</h2>
            </div>
            <span class="bg-[#E63946] text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $pending->count() }}</span>
            <div class="flex-1 h-px bg-red-200"></div>
        </div>

        <div class="space-y-3">
            @foreach($pending as $activity)
            <div class="bg-white rounded-lg shadow-sm border-l-4 border-[#F5C518] overflow-hidden"
                 wire:key="pending-{{ $activity->id }}">
                <div class="p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $activity->title }}</h3>
                                @if($activity->category)
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $activity->category }}</span>
                                @endif
                                <x-status-badge status="pending" />
                            </div>
                            @if($activity->description)
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $activity->description }}</p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            @livewire('activity-status-updater', [
                                'activity'      => $activity,
                                'date'          => $date,
                                'currentStatus' => 'pending',
                            ], key("updater-pending-{$activity->id}"))
                        </div>
                    </div>

                    {{-- Timeline of today's updates --}}
                    @if($activity->day_logs->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Update History</p>
                        @foreach($activity->day_logs as $log)
                            <x-activity-timeline-item :log="$log" />
                        @endforeach
                    </div>
                    @else
                    <p class="mt-3 text-xs text-gray-400 italic">No updates yet today.</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── DONE SECTION ────────────────────────────────────────── --}}
    @if($done->isNotEmpty())
    <section>
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1B6B3A]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <h2 class="text-sm font-bold text-[#1B6B3A] uppercase tracking-widest">Completed</h2>
            </div>
            <span class="bg-[#1B6B3A] text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $done->count() }}</span>
            <div class="flex-1 h-px bg-green-200"></div>
        </div>

        <div class="space-y-2">
            @foreach($done as $activity)
            <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden opacity-80 hover:opacity-100 transition-opacity duration-150"
                 wire:key="done-{{ $activity->id }}">
                <div class="p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-medium text-gray-700 truncate">{{ $activity->title }}</h3>
                                @if($activity->category)
                                <span class="text-xs bg-white text-gray-500 border border-gray-200 px-2 py-0.5 rounded-full">{{ $activity->category }}</span>
                                @endif
                                <x-status-badge status="done" />
                            </div>
                            @if($activity->latest_log && $activity->latest_log->actor_name)
                            <p class="text-xs text-gray-400 mt-1">
                                Completed by <span class="font-medium text-gray-500">{{ $activity->latest_log->actor_name }}</span>
                                at {{ $activity->latest_log->created_at->format('H:i') }}
                                @if($activity->latest_log->remark)
                                — "{{ Str::limit($activity->latest_log->remark, 80) }}"
                                @endif
                            </p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            @livewire('activity-status-updater', [
                                'activity'      => $activity,
                                'date'          => $date,
                                'currentStatus' => 'done',
                            ], key("updater-done-{$activity->id}"))
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    @endif

    {{-- ── Audit Log Timeline (Admin & Lead only) ──────────────── --}}
    @if(auth()->user()->canManageActivities() && isset($recentAudits) && $recentAudits->isNotEmpty())
    <section class="mt-10 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-3">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#1B6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Recent Audit Trail (Supervisor Console)
            </h2>
            <span class="text-xs text-gray-400">Live operational events</span>
        </div>
        <div class="flow-root">
            <ul role="list" class="-mb-8">
                @foreach($recentAudits as $audit)
                <li>
                    <div class="relative pb-8">
                        @if(!$loop->last)
                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full bg-green-50 flex items-center justify-center ring-8 ring-white">
                                    <svg class="w-4 h-4 text-[#1B6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                <div>
                                    <p class="text-sm text-gray-800">
                                        <span class="font-semibold text-gray-900">{{ $audit->actor_name }}</span>
                                        <span class="capitalize text-gray-500 font-medium ml-1">({{ $audit->event }})</span>
                                        <span class="text-gray-600 ml-1">
                                            {{ class_basename($audit->subject_type) }} #{{ $audit->subject_id }}
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right text-xs whitespace-nowrap text-gray-400 font-mono">
                                    {{ $audit->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </section>
    @endif

    {{-- wire:poll auto-refreshes the board every 30s for live shift updates --}}
    <div wire:poll.30000ms></div>
</div>
