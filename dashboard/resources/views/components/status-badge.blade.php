@props(['status'])
@php
    $classes = match($status) {
        'done'    => 'bg-[#1B6B3A] text-white',
        'pending' => 'bg-[#F5C518] text-gray-900',
        default   => 'bg-gray-200 text-gray-700',
    };
    $label = match($status) {
        'done'    => 'Done',
        'pending' => 'Pending',
        default   => ucfirst($status),
    };
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold $classes"]) }}>
    @if($status === 'done')
        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
    @else
        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
    @endif
    {{ $label }}
</span>
