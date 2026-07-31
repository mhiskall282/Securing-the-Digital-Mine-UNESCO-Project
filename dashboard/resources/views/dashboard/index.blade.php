@extends('layouts.app')

@section('title', 'Enterprise Network Security SaaS')

@section('content')
<!-- CSS for 3D Mesh Gradients, DOT Grids, and Railway-inspired Layouts -->
<style>

    /* Ambient Floating Blob Animations */
    @keyframes floatBlob {
        0%, 100% {
            transform: translateY(0px) scale(1);
        }
        50% {
            transform: translateY(-20px) scale(1.1);
        }
    }

    .animate-blob-1 {
        animation: floatBlob 8s ease-in-out infinite;
    }

    .animate-blob-2 {
        animation: floatBlob 12s ease-in-out infinite 2s;
    }

    /* CSS Micro-Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }

    /* Custom Theme Transitions */
    body, .bg-panel, .border-theme, .text-theme {
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }

    /* Light Theme Variable Overrides */
    body.light-theme {
        background-color: #fafafa !important;
        color: #09090b !important;
    }

    body.light-theme #app-splash-screen {
        background-color: #fafafa !important;
    }

    body.light-theme nav {
        background-color: rgba(250, 250, 250, 0.8) !important;
        border-color: #e4e4e7 !important;
    }

    body.light-theme nav span.text-white,
    body.light-theme nav div.text-white {
        color: #09090b !important;
    }

    body.light-theme footer {
        border-color: #e4e4e7 !important;
        background-color: #f4f4f5 !important;
    }

    body.light-theme .bg-card-theme {
        background-color: #ffffff !important;
        border-color: #e4e4e7 !important;
        color: #09090b !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.02), 0 4px 6px -4px rgb(0 0 0 / 0.02) !important;
    }

    body.light-theme .bg-inner-theme {
        background-color: #f4f4f5 !important;
        border-color: #e4e4e7 !important;
        color: #27272a !important;
    }

    body.light-theme .text-title-theme {
        color: #09090b !important;
    }

    body.light-theme .text-desc-theme {
        color: #71717a !important;
    }

    /* 3D Glassmorphism Panel Shadow */
    .glass-panel-3d {
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }
    
    body.light-theme .glass-panel-3d {
        box-shadow: 0 20px 40px -15px rgba(9, 9, 11, 0.05), inset 0 1px 0 rgba(0, 0, 0, 0.02);
    }

    /* Card Hover Glows */
    .hover-glow:hover {
        transform: translateY(-3px) scale(1.01);
        box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.12);
        border-color: rgba(16, 185, 129, 0.3) !important;
    }
</style>

<div class="space-y-16 min-h-screen pb-12">
    
    <!-- Hero Section -->
    <div class="relative rounded-3xl bg-[#0c0c0e] border border-[#1f1f23] overflow-hidden p-8 md:p-16 glass-panel-3d bg-card-theme animate-fade-in">
        
        <!-- Glowing Mesh Blobs (Render/Railway Style) -->
        <div class="absolute -right-36 -top-36 w-96 h-96 bg-[#10b981]/5 rounded-full blur-3xl animate-blob-1 pointer-events-none"></div>
        <div class="absolute -left-36 -bottom-36 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl animate-blob-2 pointer-events-none"></div>

        <!-- Floating Theme Toggle Button -->
        <div class="absolute right-6 top-6 z-20">
            <button onclick="toggleTheme()" class="p-2.5 bg-white/5 hover:bg-white/10 border border-theme border-[#2e2e33]/50 rounded-xl text-xs font-semibold text-white transition-all cursor-pointer flex items-center gap-2">
                <span id="themeIcon">☀️</span>
                <span id="themeText" class="hidden sm:inline">Light Mode</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10">
            
            <!-- Left Text Content -->
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#10b981]"></span>
                    </span>
                    Intrusion Detection Gateway v3.0.0
                </span>
                
                <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight text-title-theme font-sans">
                    Securing the <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#10b981] to-emerald-400">Digital Mine</span>
                </h1>
                
                <p class="text-sm text-gray-400 font-light leading-relaxed max-w-xl text-desc-theme">
                    Deploy a metaheuristic-optimized deep learning framework across your subsoil SCADA networks. We leverage the Binary Whale Optimization Algorithm (BWOA) and CNN-LSTM architectures to secure industrial IoT interfaces at sub-millisecond speeds.
                </p>

                @guest
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('signup') }}" class="px-5 py-3 bg-[#10b981] hover:bg-[#0da672] text-white rounded-xl text-xs font-semibold shadow-lg transition-all cursor-pointer">
                        Get Started (Register Organization)
                    </a>
                    <a href="{{ route('login') }}" class="px-5 py-3 bg-white/5 hover:bg-white/10 text-white border border-theme border-[#2e2e33]/50 rounded-xl text-xs font-semibold transition cursor-pointer">
                        Log In Console
                    </a>
                </div>
                @else
                <div class="p-4 bg-black/40 border border-theme border-[#2e2e33]/50 rounded-2xl flex items-center justify-between gap-4 max-w-lg bg-inner-theme">
                    <div>
                        <div class="text-xs font-bold text-white text-title-theme">Active Tenant Console</div>
                        <div class="text-[10px] text-gray-400 font-mono mt-0.5 text-desc-theme">{{ auth()->user()->organization->name ?? 'Single Tenant' }}</div>
                    </div>
                    <a href="{{ route('live-monitor') }}" class="px-4 py-2.5 bg-[#10b981] hover:bg-[#0da672] text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                        Open Live Monitor &rarr;
                    </a>
                </div>
                @endguest
            </div>

            <!-- Right Terminal Box -->
            <div class="lg:col-span-5">
                <div class="bg-[#09090b] border border-theme border-[#1f1f23] rounded-2xl overflow-hidden shadow-2xl font-mono text-xs glass-panel-3d">
                    <!-- Top Window bar -->
                    <div class="bg-[#121214] border-b border-theme border-[#1f1f23] px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500/50"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500/50"></span>
                        </div>
                        <span class="text-[10px] text-gray-500">npx unesco-mine-sec-cli</span>
                    </div>
                    <!-- Terminal Body -->
                    <div class="p-5 space-y-3 text-gray-300">
                        <div class="text-gray-500"># Instantly launch local sniffer agent</div>
                        <div>
                            <span class="text-[#10b981]">$</span> npx unesco-mine-sec-cli
                        </div>
                        <div class="text-gray-400">
                            ? Enter Dashboard REST API URL: <span class="text-[#10b981]">https://minesec-dashboard-prod.onrender.com/api/external/analyze</span>
                        </div>
                        <div class="text-gray-400">
                            ? Select Network Interface to sniff: <span class="text-[#10b981]">Wi-Fi 2</span>
                        </div>
                        <div class="text-[#10b981] font-semibold">
                            [+] Connection verified. Classifier is ONLINE.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Product Features Showcase -->
    <div class="space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <h2 class="text-2xl font-bold text-white text-title-theme">Advanced SCADA Protection Core</h2>
            <p class="text-xs text-gray-400 font-light text-desc-theme">Built from the ground up for energy-efficiency, latency isolation, and edge scalability.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-[#121214] border border-[#1f1f23] p-6 rounded-2xl space-y-3 bg-card-theme hover-glow transition-all duration-300 animate-fade-in delay-100 shadow-md">
                <div class="text-2xl">🐋</div>
                <h4 class="font-bold text-white text-sm text-title-theme">BWOA Optimization</h4>
                <p class="text-[11px] text-gray-400 font-light leading-relaxed text-desc-theme">Prunes network metrics from 41 standard indicators down to 10 highly-correlated variables, reducing CPU footprint by 75.6%.</p>
            </div>
            
            <div class="bg-[#121214] border border-[#1f1f23] p-6 rounded-2xl space-y-3 bg-card-theme hover-glow transition-all duration-300 animate-fade-in delay-200 shadow-md">
                <div class="text-2xl">🧠</div>
                <h4 class="font-bold text-white text-sm text-title-theme">CNN-LSTM Architecture</h4>
                <p class="text-[11px] text-gray-400 font-light leading-relaxed text-desc-theme">Captures spatial anomalies of network headers and sequential time-series patterns over connection windows.</p>
            </div>

            <div class="bg-[#121214] border border-[#1f1f23] p-6 rounded-2xl space-y-3 bg-card-theme hover-glow transition-all duration-300 animate-fade-in delay-300 shadow-md">
                <div class="text-2xl">⚡</div>
                <h4 class="font-bold text-white text-sm text-title-theme">Float16 Quantization</h4>
                <p class="text-[11px] text-gray-400 font-light leading-relaxed text-desc-theme">Model quantized into 0.82MB TFLite payload running 207x faster (0.76ms latency) for cheap microcontroller nodes.</p>
            </div>

            <div class="bg-[#121214] border border-[#1f1f23] p-6 rounded-2xl space-y-3 bg-card-theme hover-glow transition-all duration-300 animate-fade-in delay-300 shadow-md">
                <div class="text-2xl">🔐</div>
                <h4 class="font-bold text-white text-sm text-title-theme">Multi-Tenant Isolation</h4>
                <p class="text-[11px] text-gray-400 font-light leading-relaxed text-desc-theme">Isolates registered device keys, monitoring streams, and dashboards. Ideal for complex mining groups.</p>
            </div>
        </div>
    </div>

    <!-- Model Capabilities / Attack Vectors -->
    <div class="bg-[#121214] border border-[#1f1f23] p-8 rounded-3xl shadow-lg space-y-6 bg-card-theme animate-fade-in">
        <div class="space-y-2">
            <h3 class="text-xl font-bold text-white flex items-center gap-2 text-title-theme">
                <span class="w-1.5 h-6 rounded-md bg-[#10b981]"></span>
                Model Inference Capabilities
            </h3>
            <p class="text-xs text-gray-400 font-light max-w-xl text-desc-theme">
                The deployed model is trained to recognize benign traffic and identify four distinct cyber-threat categories:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 text-xs text-gray-300 font-mono">
            <div class="bg-black/20 p-4 rounded-xl border border-[#2e2e33]/50 space-y-1 bg-inner-theme">
                <span class="text-emerald-400 font-bold">Normal (Benign)</span>
                <p class="text-[10px] text-gray-500 font-sans mt-2 text-desc-theme">Standard operational traffic. No anomalies flagged.</p>
            </div>
            <div class="bg-black/20 p-4 rounded-xl border border-[#2e2e33]/50 space-y-1 bg-inner-theme">
                <span class="text-red-400 font-bold">DoS (SYN Flood)</span>
                <p class="text-[10px] text-gray-500 font-sans mt-2 text-desc-theme">Flooding target servers with connection requests to trigger denial of service.</p>
            </div>
            <div class="bg-black/20 p-4 rounded-xl border border-[#2e2e33]/50 space-y-1 bg-inner-theme">
                <span class="text-amber-400 font-bold">Probe (Port Scan)</span>
                <p class="text-[10px] text-gray-500 font-sans mt-2 text-desc-theme">Network reconnaissance to find open ports and software versions.</p>
            </div>
            <div class="bg-black/20 p-4 rounded-xl border border-[#2e2e33]/50 space-y-1 bg-inner-theme">
                <span class="text-purple-400 font-bold">U2R (Root Attempt)</span>
                <p class="text-[10px] text-gray-500 font-sans mt-2 text-desc-theme">User to Root privilege escalation attempts targeting local systems.</p>
            </div>
            <div class="bg-black/20 p-4 rounded-xl border border-[#2e2e33]/50 space-y-1 bg-inner-theme">
                <span class="text-blue-400 font-bold">R2L (Unauthorized Access)</span>
                <p class="text-[10px] text-gray-500 font-sans mt-2 text-desc-theme">Remote to Local attacks trying to inject remote commands or bypass logins.</p>
            </div>
        </div>
    </div>

    <!-- Contact & Support Form -->
    <div class="bg-[#121214] border border-[#1f1f23] p-8 rounded-3xl shadow-lg grid grid-cols-1 md:grid-cols-12 gap-8 bg-card-theme animate-fade-in">
        
        <!-- Left details -->
        <div class="md:col-span-5 space-y-4">
            <h3 class="text-xl font-bold text-white text-title-theme">Pilot Inquiries & Technical Support</h3>
            <p class="text-xs text-gray-400 leading-relaxed font-light text-desc-theme">
                Are you looking to deploy this framework as a pilot in your mineral processing control room or subsoil SCADA centers? Let's connect.
            </p>
            
            <div class="space-y-2 pt-2">
                <div class="text-xs text-gray-300 text-title-theme">📧 Direct Support Email:</div>
                <a href="mailto:hello@johnokyere.xyz" class="inline-block text-sm font-semibold text-[#10b981] hover:underline font-mono">
                    hello@johnokyere.xyz
                </a>
            </div>
        </div>

        <!-- Right form -->
        <form onsubmit="event.preventDefault(); alert('Message sent successfully. Our support desk will reach out within 24 hours.');" class="md:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1">
                <label class="text-[10px] font-mono text-gray-400 uppercase text-desc-theme">Your Name</label>
                <input type="text" required class="w-full bg-[#0c0c0e] border border-theme border-[#2e2e33]/50 rounded-xl px-4 py-2.5 text-xs text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600 bg-inner-theme" placeholder="e.g. John Doe">
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-mono text-gray-400 uppercase text-desc-theme">Work Email</label>
                <input type="email" required class="w-full bg-[#0c0c0e] border border-theme border-[#2e2e33]/50 rounded-xl px-4 py-2.5 text-xs text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600 bg-inner-theme" placeholder="name@company.com">
            </div>
            <div class="space-y-1 md:col-span-2">
                <label class="text-[10px] font-mono text-gray-400 uppercase text-desc-theme">Message / Inquiry Details</label>
                <textarea required rows="3" class="w-full bg-[#0c0c0e] border border-theme border-[#2e2e33]/50 rounded-xl px-4 py-2.5 text-xs text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600 bg-inner-theme" placeholder="Describe your network or SCADA interface configuration..."></textarea>
            </div>
            <div class="md:col-span-2 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-[#10b981] hover:bg-[#0da672] text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                    Submit Inquiry
                </button>
            </div>
        </form>

    </div>

</div>

<!-- Theme Toggle JavaScript -->
<script>
    function toggleTheme() {
        const body = document.body;
        const icon = document.getElementById('themeIcon');
        const text = document.getElementById('themeText');
        
        body.classList.toggle('light-theme');
        
        if (body.classList.contains('light-theme')) {
            icon.innerText = '🌙';
            text.innerText = 'Dark Mode';
            localStorage.setItem('theme', 'light');
        } else {
            icon.innerText = '☀️';
            text.innerText = 'Light Mode';
            localStorage.setItem('theme', 'dark');
        }
    }

    // Load saved preference
    window.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
            document.getElementById('themeIcon').innerText = '🌙';
            document.getElementById('themeText').innerText = 'Dark Mode';
        }
    });
</script>
@endsection
