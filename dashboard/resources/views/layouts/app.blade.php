<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Securing the Digital Mine — A Metaheuristic Optimized Deep Learning Framework for Intrusion Detection in IoT Enabled Mineral Resource Operations">
    <title>@yield('title', 'Securing the Digital Mine') — SaaS Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            mermaid.initialize({ startOnLoad: true, theme: 'dark' });
        });
    </script>
    <!-- MathJax for rendering math formulas -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']]
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- Global Light Mode CSS Overrides -->
    <style>
        body {
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px) !important;
            background-size: 24px 24px !important;
        }
        body.light-theme {
            background-color: #fafafa !important;
            background-image: radial-gradient(rgba(9, 9, 11, 0.08) 1px, transparent 1px) !important;
            color: #09090b !important;
        }
        body.light-theme h1,
        body.light-theme h2,
        body.light-theme h3,
        body.light-theme h4,
        body.light-theme h5,
        body.light-theme h6,
        body.light-theme span,
        body.light-theme p,
        body.light-theme td,
        body.light-theme th,
        body.light-theme label,
        body.light-theme li,
        body.light-theme strong,
        body.light-theme code {
            color: #09090b !important;
        }
        body.light-theme p,
        body.light-theme span.text-desc-theme,
        body.light-theme .text-gray-400,
        body.light-theme .text-gray-500 {
            color: #52525b !important;
        }
        body.light-theme .bg-[#101726],
        body.light-theme .bg-[#17171a] {
            background-color: #ffffff !important;
            border-color: #e4e4e7 !important;
            color: #09090b !important;
        }
        body.light-theme .bg-black\/20,
        body.light-theme .bg-black\/35,
        body.light-theme .bg-black\/40 {
            background-color: #f4f4f5 !important;
            border-color: #e4e4e7 !important;
            color: #09090b !important;
        }
        body.light-theme input,
        body.light-theme select,
        body.light-theme textarea {
            background-color: #ffffff !important;
            border-color: #e4e4e7 !important;
            color: #09090b !important;
        }
        body.light-theme .divide-white\/5 {
            --tw-divide-y-reverse: 0;
            border-bottom-width: calc(1px * var(--tw-divide-y-reverse));
            border-top-width: calc(1px * calc(1 - var(--tw-divide-y-reverse)));
            border-color: #e4e4e7 !important;
        }
    </style>
</head>
<body class="h-full flex flex-col bg-[#0c0c0e] text-[#ededef] font-sans antialiased selection:bg-[#10b981]/30 selection:text-white">
    
    <!-- Splash Screen Loader -->
    <div id="app-splash-screen" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-[#0c0c0e] transition-opacity duration-500">
        <div class="flex flex-col items-center gap-4 text-center">
            <div class="relative flex items-center justify-center w-20 h-20 rounded-2xl bg-[#10b981]/10 border border-[#10b981]/25 shadow-2xl animate-pulse">
                <svg class="w-12 h-12 text-[#10b981]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Securing the Digital Mine</h1>
                <p class="text-xs text-[#10b981] font-mono tracking-widest uppercase mt-1">Enterprise SaaS Platform</p>
            </div>
        </div>
    </div>

    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-40 bg-[#17171a]/80 backdrop-blur-md border-b border-[#2e2e33]/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#10b981]/10 border border-[#10b981]/25 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#10b981]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-white tracking-wide">MineSec</span>
                            <span class="text-[9px] text-[#10b981] font-mono leading-none tracking-widest uppercase">SaaS Gateway</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-1.5">
                    <a href="{{ route('dashboard') }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-200
                              {{ request()->routeIs('dashboard') ? 'bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        Overview
                    </a>

                    @auth
                    <a href="{{ route('live-monitor') }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-200
                              {{ request()->routeIs('live-monitor') ? 'bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        Live Monitor
                    </a>
                    @endauth

                    <a href="{{ route('research') }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-200
                              {{ request()->routeIs('research') ? 'bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        Research & Insights
                    </a>

                    <a href="{{ route('api-docs') }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all duration-200
                              {{ request()->routeIs('api-docs') ? 'bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        API & CLI Docs
                    </a>

                    @auth
                    <!-- Tools Dropdown Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                                class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-1 transition-all duration-200
                                       {{ (request()->routeIs('devices') || request()->routeIs('bwoa') || request()->routeIs('simulator')) ? 'bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                            Tools Console
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-cloak
                             class="absolute left-0 mt-2 w-48 bg-[#17171a] border border-[#2e2e33] rounded-2xl shadow-xl py-2 z-50 animate-fade-in space-y-1">
                            <a href="{{ route('devices') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-300 hover:text-white hover:bg-white/5 transition">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#10b981]"></span>
                                Device Nodes
                            </a>
                            <a href="{{ route('bwoa') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-300 hover:text-white hover:bg-white/5 transition">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                BWOA Optimizer
                            </a>
                            <a href="{{ route('simulator') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-300 hover:text-white hover:bg-white/5 transition">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                                Detection Simulator
                            </a>
                        </div>
                    </div>
                    @endauth
                </div>

                <!-- Right Side Profile Info & Log Out -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                    <a href="{{ route('admin-dashboard') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-200 bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                        Admin Panel
                    </a>
                    
                    <a href="{{ route('settings') }}" title="Account Settings"
                       class="p-2 rounded-xl text-xs font-semibold transition-all duration-200
                              {{ request()->routeIs('settings') ? 'bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20' : 'bg-white/5 text-gray-400 hover:text-white border border-white/10 hover:bg-white/10' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-white/5 border border-white/10 hover:bg-white/10 text-gray-300 hover:text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                            Log Out
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-[#10b981] hover:bg-[#0da672] text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                        Console Login
                    </a>
                    @endauth
                </div>
                
                <!-- Mobile Navigation Toggle -->
                <div class="flex items-center md:hidden">
                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="p-2 rounded-xl bg-white/5 border border-[#2e2e33] text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-[#2e2e33] bg-[#0c0c0e] px-4 py-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-gray-300 hover:bg-white/5">Overview</a>
            
            @auth
            <a href="{{ route('live-monitor') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-gray-300 hover:bg-white/5">Live Monitor</a>
            <a href="{{ route('devices') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-gray-300 hover:bg-white/5">Device Nodes</a>
            <a href="{{ route('bwoa') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-gray-300 hover:bg-white/5">BWOA Optimizer</a>
            <a href="{{ route('simulator') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-gray-300 hover:bg-white/5">Detection Simulator</a>
            @endauth

            <a href="{{ route('api-docs') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-gray-300 hover:bg-white/5">API & CLI Docs</a>
            <a href="{{ route('research') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-gray-300 hover:bg-white/5">Research</a>
            
            @auth
            <a href="{{ route('admin-dashboard') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-red-400 hover:bg-white/5">Admin Panel</a>
            <a href="{{ route('settings') }}" class="block px-4 py-2 rounded-xl text-sm font-semibold text-emerald-400 hover:bg-white/5">Settings & Profile</a>
            <div class="border-t border-[#2e2e33] my-2 pt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm font-semibold text-red-400 hover:bg-white/5">Log Out</button>
                </form>
            </div>
            @else
            <div class="border-t border-[#2e2e33] my-2 pt-2">
                <a href="{{ route('login') }}" class="block text-center px-4 py-2 bg-[#10b981] hover:bg-[#0da672] text-white rounded-xl text-sm font-semibold transition">Console Login</a>
            </div>
            @endauth
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-[#2e2e33] bg-[#0c0c0e] py-6 text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <span>&copy; {{ date('Y') }} Securing the Digital Mine. Built with Laravel, Livewire, and Tailwind CSS.</span>
            </div>
            <div class="flex items-center gap-6">
                <span>Russian-African Forum-Contest of Young Scientists 2026</span>
                <span class="text-gray-600 font-mono">v3.0.0-saas</span>
            </div>
        </div>
    </footer>

    @livewireScripts
    
    <!-- Loader Fade Out Script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('app-splash-screen');
            if (loader) {
                setTimeout(() => {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.remove();
                    }, 500);
                }, 400);
            }
        });
    </script>
</body>
</html>
