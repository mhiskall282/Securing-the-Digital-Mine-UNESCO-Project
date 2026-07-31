<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Support Tracker') }} — Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-[#0F1A14]">
    {{-- Splash Screen Loader for Guest/Auth --}}
    <div id="guest-splash-screen" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-[#0F1A14] transition-opacity duration-500">
        <div class="flex flex-col items-center gap-4 text-center">
            <div class="relative flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10 border border-white/20 shadow-2xl">
                <svg class="w-10 h-10 text-[#F5C518] animate-bounce" viewBox="0 0 32 32" fill="currentColor">
                    <polygon points="16,3 30,27 2,27"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Support Tracker</h1>
                <p class="text-xs text-[#F5C518] font-mono tracking-widest uppercase mt-0.5">Npontu Technologies</p>
            </div>
            <div class="flex items-center gap-1.5 mt-2">
                <div class="w-2 h-2 rounded-full bg-[#1B6B3A] animate-ping"></div>
                <span class="text-xs text-gray-400 font-medium">Initializing Security Portal...</span>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('load', () => {
            const splash = document.getElementById('guest-splash-screen');
            if (splash) {
                setTimeout(() => {
                    splash.style.opacity = '0';
                    setTimeout(() => splash.remove(), 600);
                }, 7000);
            }
        });
    </script>
    {{ $slot }}
</body>
</html>
