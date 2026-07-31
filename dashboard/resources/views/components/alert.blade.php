@props(['type' => 'success', 'message'])
@php
    $classes = match($type) {
        'success' => 'bg-green-50 border-[#1B6B3A] text-green-800',
        'error'   => 'bg-red-50 border-[#E63946] text-red-800',
        'warning' => 'bg-yellow-50 border-[#F5C518] text-yellow-800',
        default   => 'bg-blue-50 border-blue-500 text-blue-800',
    };
@endphp
<div {{ $attributes->merge(['class' => "border-l-4 p-4 rounded-r-md $classes flex items-start gap-3"]) }}
     role="alert" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex-1 text-sm font-medium">{{ $message }}</div>
    <button @click="show = false" class="text-current opacity-60 hover:opacity-100 transition-opacity text-xs">✕</button>
</div>
