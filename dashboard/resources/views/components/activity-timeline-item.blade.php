@props(['log'])
<div class="flex gap-3 text-sm">
    {{-- Timeline dot --}}
    <div class="flex-shrink-0 mt-1">
        <div class="w-2 h-2 rounded-full {{ $log->isDone() ? 'bg-[#1B6B3A]' : 'bg-[#F5C518]' }} mt-1.5 ring-2 ring-offset-1 ring-current opacity-60"></div>
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <x-status-badge :status="$log->status" />
            <span class="text-gray-500 text-xs font-mono">{{ $log->created_at->format('H:i') }}</span>
        </div>
        <p class="text-gray-700 mt-1">
            <span class="font-medium text-gray-900">{{ $log->actor_name }}</span>
            <span class="text-gray-500 text-xs ml-1">({{ $log->actor_role }}{{ $log->actor_designation ? ' · ' . $log->actor_designation : '' }})</span>
        </p>
        @if($log->remark)
        <p class="text-gray-600 mt-0.5 italic text-xs leading-relaxed">
            "{{ $log->remark }}"
        </p>
        @endif
    </div>
</div>
