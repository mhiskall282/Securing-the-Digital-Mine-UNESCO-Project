@extends('layouts.app')

@section('title', 'Developer & System Documentation Portal')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start animate-fade-in">
    
    <!-- Left Navigation Sidebar (Supabase Style) -->
    <aside class="lg:col-span-3 sticky top-24 space-y-6">
        <div class="bg-[#17171a] border border-[#2e2e33] rounded-2xl p-5 space-y-4">
            <h4 class="text-xs font-mono font-bold uppercase tracking-wider text-gray-500">Documentation Portal</h4>
            <nav class="flex flex-col gap-1 text-xs font-semibold">
                <a href="#site-guide" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>📖</span> Site & SaaS Workflow Guide
                </a>
                <a href="#tokens" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>🔑</span> Authentication & Tokens
                </a>
                <a href="#cli-setup" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>🔌</span> CLI Scanner & Help Flags
                </a>
                <a href="#rpi-deploy" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>🍓</span> Raspberry Pi Edge Setup
                </a>
                <a href="#layer-spec" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>🧱</span> CNN-LSTM Layer Architecture
                </a>
                <a href="#api-reference" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>📡</span> REST Gateway Reference
                </a>
                <a href="#bypass-api" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>🧠</span> Model Server Bypass API
                </a>
                <a href="#sdk-reference" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>🐍</span> Python Core SDK Reference
                </a>
                <a href="#status-codes" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>⚠️</span> HTTP Status Codes
                </a>
                <a href="#cloud-deploy" class="px-3 py-2 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition flex items-center gap-2">
                    <span>☁️</span> Render Cloud Deployment
                </a>
            </nav>
            <div class="border-t border-[#2e2e33]/50 my-2 pt-2">
                <a href="https://github.com/mhiskall282/unesco-project" target="_blank" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-black/45 hover:bg-black/60 border border-white/5 rounded-xl text-xs font-semibold text-white transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0012 2z"/></svg>
                    GitHub Codebase
                </a>
            </div>
        </div>

        <div class="p-5 bg-gradient-to-br from-[#10b981]/5 to-transparent border border-[#2e2e33] rounded-2xl space-y-2">
            <h5 class="text-xs font-bold text-white">Need Technical Support?</h5>
            <p class="text-[10px] text-gray-400 font-light leading-relaxed">Contact lead maintainer John Okyere for SCADA integration pilots.</p>
            <a href="mailto:hello@johnokyere.xyz" class="text-xs font-bold text-[#10b981] hover:underline font-mono">hello@johnokyere.xyz</a>
        </div>
    </aside>

    <!-- Center Content Area (Supabase Docs Style) -->
    <main class="lg:col-span-9 space-y-12">
        
        <!-- Section: Step-by-Step Site & SaaS Guide -->
        <section id="site-guide" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">01. Step-by-Step Workflow</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">How the Platform & Site Works</h2>
            </div>
            
            <p class="text-sm text-gray-400 leading-relaxed font-light">
                Securing the Digital Mine operates as an enterprise multi-tenant cybersecurity portal. Follow this step-by-step operational guide:
            </p>

            <div class="space-y-6">
                <!-- Step 1 -->
                <div class="bg-black/35 border border-[#2e2e33] p-5 rounded-2xl space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#10b981]/15 text-[#10b981] font-mono text-xs font-bold flex items-center justify-center">1</span>
                        <h4 class="text-sm font-bold text-white">Register Organization & Account (<code class="text-emerald-400">/signup</code>)</h4>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed pl-9">
                        Navigate to the signup portal to create a private organization account. Multi-tenant database schemas isolate all subsequent device registrations, security logs, and telemetry data strictly to your organization ID.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-black/35 border border-[#2e2e33] p-5 rounded-2xl space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#10b981]/15 text-[#10b981] font-mono text-xs font-bold flex items-center justify-center">2</span>
                        <h4 class="text-sm font-bold text-white">Provision Device Nodes & Tokens (<code class="text-emerald-400">/devices</code>)</h4>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed pl-9">
                        Log into the console, access the **Device Nodes** manager, and register your physical hardware nodes (e.g. *Shaft 4 Pi TAP*). The system generates a unique Bearer Token (e.g. <code>unesco_device_xxxx...</code>) used to authenticate telemetry transmissions.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-black/35 border border-[#2e2e33] p-5 rounded-2xl space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#10b981]/15 text-[#10b981] font-mono text-xs font-bold flex items-center justify-center">3</span>
                        <h4 class="text-sm font-bold text-white">Launch Edge CLI Sniffer (<code class="text-emerald-400">unesco-mine-sec-cli</code>)</h4>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed pl-9">
                        Install the sniffer tool on edge appliances via npm, Yarn, or pnpm. The client inspects local interface telemetry, prunes metrics down to 10 BWOA features, and streams JSON flows to <code>/api/external/analyze</code>.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-black/35 border border-[#2e2e33] p-5 rounded-2xl space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#10b981]/15 text-[#10b981] font-mono text-xs font-bold flex items-center justify-center">4</span>
                        <h4 class="text-sm font-bold text-white">Real-Time Livewire Monitoring (<code class="text-emerald-400">/live-monitor</code>)</h4>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed pl-9">
                        The API gateway validates tokens, queries the Python inference server (port 8001), logs predictions to database tables, and dynamically updates the Livewire monitor feed with sub-millisecond latencies.
                    </p>
                </div>

                <!-- Step 5 -->
                <div class="bg-black/35 border border-[#2e2e33] p-5 rounded-2xl space-y-2">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#10b981]/15 text-[#10b981] font-mono text-xs font-bold flex items-center justify-center">5</span>
                        <h4 class="text-sm font-bold text-white">Detection Simulator & Research (<code class="text-emerald-400">/simulator</code> & <code class="text-emerald-400">/research</code>)</h4>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed pl-9">
                        Use the interactive simulator to test custom attack payloads (DoS, Probe, U2R, R2L) or visit the research section to review empirical figures, BWOA fitness curves, and multi-class confusion matrices.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section: Tokens -->
        <section id="tokens" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">02. Authentication & Keys</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">Obtaining Device Bearer Tokens</h2>
            </div>
            
            <p class="text-sm text-gray-400 leading-relaxed font-light">
                To connect an edge device node and stream packets to your organization's private telemetry dashboard, you must authenticate requests using a secure access key:
            </p>

            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="w-6 h-6 rounded-full bg-[#10b981]/10 border border-[#10b981]/25 flex items-center justify-center text-[10px] font-bold text-[#10b981] font-mono shrink-0">1</div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-semibold text-white">Generate your Device Node Key</h4>
                        <p class="text-xs text-gray-400 font-light">Navigate to the **Device Nodes** tab inside the top console, input a descriptive label (e.g. *Pi shaft 4*), and generate your key.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-6 h-6 rounded-full bg-[#10b981]/10 border border-[#10b981]/25 flex items-center justify-center text-[10px] font-bold text-[#10b981] font-mono shrink-0">2</div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-semibold text-white">Secure your Bearer Token</h4>
                        <p class="text-xs text-gray-400 font-light">Copy the token instantly (e.g. <code>unesco_device_xxxx...</code>). It is hashed in the database and shown only once for security integrity.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section: CLI Setup & Help Commands -->
        <section id="cli-setup" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">03. CLI Setup & Help Commands</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">Installing & Executing the CLI Packet Scanner</h2>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed font-light">
                Deploy our lightweight packet sniffer and streamer client directly on your edge monitoring appliances. You can install it using any major JavaScript package manager:
            </p>

            <!-- Package Manager Tabs -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <h4 class="text-xs font-mono text-gray-300 uppercase tracking-wider">Option 1: Using npm</h4>
                    <div class="bg-black/40 border border-[#2e2e33] rounded-xl p-4 font-mono text-xs text-gray-300">
                        cd npm-packet-scanner<br>
                        npm install && npm install -g ./<br>
                        unesco-mine-sec-cli
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-mono text-gray-300 uppercase tracking-wider">Option 2: Using Yarn</h4>
                    <div class="bg-black/40 border border-[#2e2e33] rounded-xl p-4 font-mono text-xs text-gray-300">
                        cd npm-packet-scanner<br>
                        yarn install && yarn global add file:./<br>
                        unesco-mine-sec-cli
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-mono text-gray-300 uppercase tracking-wider">Option 3: Using pnpm</h4>
                    <div class="bg-black/40 border border-[#2e2e33] rounded-xl p-4 font-mono text-xs text-gray-300">
                        cd npm-packet-scanner<br>
                        pnpm install && pnpm add -g ./<br>
                        unesco-mine-sec-cli
                    </div>
                </div>
            </div>

            <!-- CLI Help & Flag Options -->
            <div class="space-y-4 pt-4 border-t border-[#2e2e33]/50">
                <h3 class="text-lg font-bold text-white">CLI Help & Command-Line Option Flags</h3>
                <p class="text-xs text-gray-400 font-light">The CLI client supports non-interactive execution via explicit command-line flags:</p>

                <div class="bg-black/40 border border-[#2e2e33] rounded-xl p-4 font-mono text-xs text-gray-300 select-all overflow-x-auto">
<pre># View help options
unesco-mine-sec-cli --help

# Output software version
unesco-mine-sec-cli --version

# Run non-interactively with custom API endpoint, key, and adapter
unesco-mine-sec-cli --url http://127.0.0.1:8000/api/external/analyze --key unesco_device_xxxx --interface Wi-Fi 2</pre>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[11px] font-mono">
                        <thead>
                            <tr class="border-b border-[#2e2e33] text-gray-500 uppercase">
                                <th class="pb-2">Flag</th>
                                <th class="pb-2">Alias</th>
                                <th class="pb-2">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2e2e33] text-gray-300">
                            <tr>
                                <td class="py-2 text-emerald-400">--help</td>
                                <td class="py-2">-h</td>
                                <td class="py-2 text-gray-400">Prints the command usage options and exits.</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-emerald-400">--version</td>
                                <td class="py-2">-v</td>
                                <td class="py-2 text-gray-400">Prints the software version (v3.0.0-saas) and exits.</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-emerald-400">--url &lt;endpoint&gt;</td>
                                <td class="py-2">-</td>
                                <td class="py-2 text-gray-400">Target REST API endpoint (default: <code>http://localhost:8000/api/external/analyze</code>).</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-emerald-400">--key &lt;token&gt;</td>
                                <td class="py-2">-</td>
                                <td class="py-2 text-gray-400">Device node API key or Bearer token generated from <code>/devices</code>.</td>
                            </tr>
                            <tr>
                                <td class="py-2 text-emerald-400">--interface &lt;name&gt;</td>
                                <td class="py-2">-</td>
                                <td class="py-2 text-gray-400">Network adapter to sniff (e.g., <code>eth0</code>, <code>Wi-Fi 2</code>).</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Section: Raspberry Pi Deployment Guide -->
        <section id="rpi-deploy" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">04. Hardware Edge Deployment</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">Raspberry Pi & Production Deployment Guide</h2>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed font-light">
                Deploying this system in resource-constrained environments (like subsoil mineral extraction zones or mine shafts) requires running the quantized models on low-power IoT gateways like the <strong>Raspberry Pi 4 / 5</strong>.
            </p>

            <div class="space-y-6">
                <!-- 1. Raspberry Pi OS Setup -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white">1. Raspberry Pi OS Setup</h4>
                    <p class="text-xs text-gray-400 font-light">Install core system dependencies and Python TFLite runtime libraries:</p>
                    <div class="bg-black/40 border border-[#2e2e33] rounded-xl p-4 font-mono text-xs text-gray-300 select-all overflow-x-auto">
<pre>sudo apt-get update && sudo apt-get install -y python3-pip python3-dev
pip3 install tflite-runtime requests</pre>
                    </div>
                </div>

                <!-- 2. Launching Lightweight Streamer -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white">2. Launching Lightweight Streamer</h4>
                    <p class="text-xs text-gray-400 font-light">Download the lightweight CLI scanner client and direct telemetry to the main organization server IP:</p>
                    <div class="bg-black/40 border border-[#2e2e33] rounded-xl p-4 font-mono text-xs text-gray-300 select-all overflow-x-auto">
<pre># Run the local scanner package globally on the Pi
npm install -g ./npm-packet-scanner
unesco-mine-sec-cli</pre>
                    </div>
                </div>

                <!-- 3. Industrial Network Mirroring -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white">3. Industrial Network Mirroring (SPAN / TAP)</h4>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">
                        To capture traffic passively without interrupting active SCADA PLC controls, configure network switch port mirroring (SPAN port) to copy all subnet packets directly to the network card of the Raspberry Pi.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section: CNN-LSTM Layer Architecture & Hyperparameters -->
        <section id="layer-spec" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">05. Neural Layer Architecture</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">CNN-LSTM Layer Configuration & Hyperparameters</h2>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed font-light">
                Structural breakdown of layer configurations, output vector shapes, and parameter counts for the quantized CNN-LSTM network:
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs font-mono">
                    <thead>
                        <tr class="border-b border-[#2e2e33] text-gray-500 uppercase">
                            <th class="pb-2">Layer</th>
                            <th class="pb-2">Configuration Details</th>
                            <th class="pb-2">Output Shape</th>
                            <th class="pb-2 text-right">Parameters</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2e2e33] text-gray-300">
                        <tr>
                            <td class="py-3 text-white font-bold">Input Layer</td>
                            <td class="py-3 text-gray-400">1D Feature Array (BWOA Mask)</td>
                            <td class="py-3">(None, 10, 1)</td>
                            <td class="py-3 text-right">0</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-white font-bold">Conv1D</td>
                            <td class="py-3 text-gray-400">64 filters, kernel size 3, relu activation</td>
                            <td class="py-3">(None, 8, 64)</td>
                            <td class="py-3 text-right text-emerald-400 font-bold">256</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-white font-bold">MaxPooling1D</td>
                            <td class="py-3 text-gray-400">pool size 2</td>
                            <td class="py-3">(None, 4, 64)</td>
                            <td class="py-3 text-right">0</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-white font-bold">LSTM</td>
                            <td class="py-3 text-gray-400">256 units, return_sequences=False</td>
                            <td class="py-3">(None, 256)</td>
                            <td class="py-3 text-right text-emerald-400 font-bold">328,704</td>
                        </tr>
                        <tr class="bg-emerald-500/5">
                            <td class="py-3 text-emerald-400 font-bold">Dense (Output)</td>
                            <td class="py-3 text-emerald-400">5 classes (Normal, DoS, Probe, U2R, R2L), Softmax</td>
                            <td class="py-3 text-emerald-400">(None, 5)</td>
                            <td class="py-3 text-right text-emerald-400 font-bold">1,285</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Section: API Reference -->
        <section id="api-reference" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">06. API Specifications</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">REST API Gateway Reference</h2>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed font-light">
                Submit raw BWOA-extracted flow features directly to our deep learning inference service via HTTP POST requests:
            </p>

            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#10b981]/15 text-[#10b981] font-mono">POST</span>
                    <code class="text-xs font-mono text-white">/api/external/analyze</code>
                </div>

                <!-- Curl request -->
                <div class="space-y-2">
                    <h4 class="text-xs font-mono text-gray-400">Request Example (curl)</h4>
                    <div class="bg-black/40 border border-[#2e2e33] rounded-xl p-4 font-mono text-[10px] text-gray-300 overflow-x-auto select-all">
<pre>curl -X POST http://localhost:8000/api/external/analyze \
  -H "Authorization: Bearer unesco_demo_token_2026" \
  -H "Content-Type: application/json" \
  -d '{
    "protocol_type": "tcp",
    "service": "http",
    "flag": "SF",
    "src_bytes": 1024,
    "hot": 0,
    "su_attempted": 0,
    "serror_rate": 0.0,
    "same_srv_rate": 1.0,
    "diff_srv_rate": 0.0,
    "dst_host_diff_srv_rate": 0.05
  }'</pre>
                    </div>
                </div>

                <!-- Response Example -->
                <div class="space-y-2">
                    <h4 class="text-xs font-mono text-gray-400">Response Payload (JSON)</h4>
                    <div class="bg-black/40 border border-[#2e2e33] rounded-xl p-4 font-mono text-[10px] text-gray-300 overflow-x-auto">
<pre>{
  "prediction": "Normal",
  "confidence": 98.45,
  "features_triggered": [],
  "latency_ms": 0.76
}</pre>
                    </div>
                </div>

                <!-- Parameters table -->
                <div class="space-y-3">
                    <h4 class="text-xs font-mono text-gray-400 uppercase tracking-wider">Payload Feature Specification</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[11px] font-mono">
                            <thead>
                                <tr class="border-b border-[#2e2e33] text-gray-500 uppercase">
                                    <th class="pb-2">Field</th>
                                    <th class="pb-2">Type</th>
                                    <th class="pb-2">Description</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2e2e33] text-gray-300">
                                <tr>
                                    <td class="py-2.5 text-white">protocol_type</td>
                                    <td class="py-2.5">string</td>
                                    <td class="py-2.5 text-gray-400">Connection protocol used. Must be <code>tcp</code>, <code>udp</code>, or <code>icmp</code>.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">service</td>
                                    <td class="py-2.5">string</td>
                                    <td class="py-2.5 text-gray-400">Destination network service (e.g. <code>http</code>, <code>private</code>, <code>telnet</code>).</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">flag</td>
                                    <td class="py-2.5">string</td>
                                    <td class="py-2.5 text-gray-400">Normal or error status indicator of the session (e.g. <code>SF</code>, <code>S0</code>).</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">src_bytes</td>
                                    <td class="py-2.5">integer</td>
                                    <td class="py-2.5 text-gray-400">Total bytes sent from the client to the destination server.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">hot</td>
                                    <td class="py-2.5">integer</td>
                                    <td class="py-2.5 text-gray-400">Number of hot/suspicious indicators (e.g., shell command attempts).</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">su_attempted</td>
                                    <td class="py-2.5">integer</td>
                                    <td class="py-2.5 text-gray-400">Attempts to escalate privileges to superuser/admin levels.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">serror_rate</td>
                                    <td class="py-2.5">float</td>
                                    <td class="py-2.5 text-gray-400">SYN error rate. Must be between <code>0.00</code> and <code>1.00</code>.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">same_srv_rate</td>
                                    <td class="py-2.5">float</td>
                                    <td class="py-2.5 text-gray-400">Rate of connections to the same service. Range: <code>0.00</code> - <code>1.00</code>.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">diff_srv_rate</td>
                                    <td class="py-2.5">float</td>
                                    <td class="py-2.5 text-gray-400">Rate of connections to different services. Range: <code>0.00</code> - <code>1.00</code>.</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-white">dst_host_diff_srv_rate</td>
                                    <td class="py-2.5">float</td>
                                    <td class="py-2.5 text-gray-400">Diff service rate on destination host. Range: <code>0.00</code> - <code>1.00</code>.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section: Bypass API -->
        <section id="bypass-api" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">07. Model Server Bypass</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">Direct Python Inference API (Port 8001)</h2>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed font-light">
                For developers building low-latency internal pipelines, you can bypass the database layer and call the Python ML Server (port 8001) directly:
            </p>

            <div class="space-y-4">
                <div class="space-y-2">
                    <h4 class="text-xs font-mono text-gray-400">1. Health Check Endpoint</h4>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#10b981]/15 text-[#10b981] font-mono">GET</span>
                        <code class="text-xs font-mono text-white">http://localhost:8001/api/health</code>
                    </div>
                </div>

                <div class="space-y-2">
                    <h4 class="text-xs font-mono text-gray-400">2. Inference Classification Endpoint</h4>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#10b981]/15 text-[#10b981] font-mono">POST</span>
                        <code class="text-xs font-mono text-white">http://localhost:8001/api/analyze</code>
                    </div>
                    <p class="text-xs text-gray-500 font-light">Requires the same JSON payload features but bypasses authentication headers for local subnet performance.</p>
                </div>
            </div>
        </section>

        <!-- Section: Python Core SDK Reference -->
        <section id="sdk-reference" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">08. Core Python SDK</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">Python Core Module Reference (<code class="text-emerald-400">src/</code>)</h2>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed font-light">
                High-level API reference for researchers and developers importing internal modules directly:
            </p>

            <div class="space-y-8">
                <!-- Optimization -->
                <div class="space-y-3 bg-black/20 p-5 rounded-2xl border border-[#2e2e33]/50">
                    <h4 class="text-xs font-mono font-bold text-emerald-400 uppercase">1. Optimization Package (src/optimization/)</h4>
                    <div class="space-y-2 text-xs font-mono text-gray-300">
                        <div>
                            <strong class="text-white">BinaryWhaleOptimizer:</strong>
                            <code class="text-[11px] block text-gray-400 mt-1">BinaryWhaleOptimizer(n_agents=30, n_features=41, max_iter=100, alpha=0.3, b=1.0)</code>
                        </div>
                        <p class="text-[11px] text-gray-400 font-sans font-light">Executes BWOA search loops, returning optimal binary feature masks and fitness history curves.</p>
                        <div>
                            <strong class="text-white">FeatureFitnessEvaluator:</strong>
                            <code class="text-[11px] block text-gray-400 mt-1">calculate_fitness(features_mask, X_train, y_train, X_val, y_val)</code>
                        </div>
                    </div>
                </div>

                <!-- Deep Models -->
                <div class="space-y-3 bg-black/20 p-5 rounded-2xl border border-[#2e2e33]/50">
                    <h4 class="text-xs font-mono font-bold text-emerald-400 uppercase">2. Deep Models Package (src/models/)</h4>
                    <div class="space-y-2 text-xs font-mono text-gray-300">
                        <div>
                            <strong class="text-white">build_cnn_lstm:</strong>
                            <code class="text-[11px] block text-gray-400 mt-1">build_cnn_lstm(input_shape=(10, 1), n_classes=5, filters=64, lstm_units=128)</code>
                        </div>
                        <div>
                            <strong class="text-white">SWaTTransferLearner:</strong>
                            <code class="text-[11px] block text-gray-400 mt-1">SWaTTransferLearner(n_swat_features=51, window_size=10, freeze_cnn_blocks=True)</code>
                        </div>
                    </div>
                </div>

                <!-- Data Pipelines -->
                <div class="space-y-3 bg-black/20 p-5 rounded-2xl border border-[#2e2e33]/50">
                    <h4 class="text-xs font-mono font-bold text-emerald-400 uppercase">3. Data Pipelines & Loaders (src/data/)</h4>
                    <div class="space-y-2 text-xs font-mono text-gray-300">
                        <div>
                            <strong class="text-white">NSLKDDLoader:</strong>
                            <code class="text-[11px] block text-gray-400 mt-1">load(path), preprocess(df), train_test_split(), normalize()</code>
                        </div>
                        <div>
                            <strong class="text-white">SWaTLoader:</strong>
                            <code class="text-[11px] block text-gray-400 mt-1">load_combined(normal_path, attack_path), sliding_window(window_size=10)</code>
                        </div>
                    </div>
                </div>

                <!-- Benchmarking -->
                <div class="space-y-3 bg-black/20 p-5 rounded-2xl border border-[#2e2e33]/50">
                    <h4 class="text-xs font-mono font-bold text-emerald-400 uppercase">4. Edge Evaluation Package (src/evaluation/)</h4>
                    <div class="space-y-2 text-xs font-mono text-gray-300">
                        <div>
                            <strong class="text-white">EdgeBenchmark:</strong>
                            <code class="text-[11px] block text-gray-400 mt-1">quantize_model(model, quantization_type="float16")</code>
                        </div>
                        <p class="text-[11px] text-gray-400 font-sans font-light">Converts Keras models to TFLite float16 payloads and verifies memory and latency constraints.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section: Status Codes -->
        <section id="status-codes" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">09. HTTP Status References</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">Status & Error Diagnostics</h2>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed font-light">
                Diagnose HTTP response codes generated during REST gateway calls:
            </p>

            <div class="space-y-4 text-xs font-mono text-gray-300">
                <div class="flex items-start gap-4">
                    <span class="text-emerald-400 font-bold w-12 shrink-0">200 OK</span>
                    <span class="text-gray-400 font-sans">Request processed successfully. Anomaly classification payload returned.</span>
                </div>
                <div class="flex items-start gap-4">
                    <span class="text-yellow-500 font-bold w-12 shrink-0">400</span>
                    <span class="text-gray-400 font-sans">Bad Request. JSON payload is malformed or missing key parameters.</span>
                </div>
                <div class="flex items-start gap-4">
                    <span class="text-red-500 font-bold w-12 shrink-0">401</span>
                    <span class="text-gray-400 font-sans">Unauthorized. Access token is invalid or inactive.</span>
                </div>
                <div class="flex items-start gap-4">
                    <span class="text-red-400 font-bold w-12 shrink-0">419</span>
                    <span class="text-gray-400 font-sans">CSRF Verification Failure. Re-check CSRF route exclusion settings in bootstrap app.php.</span>
                </div>
            </div>
        </section>

        <!-- Section: Cloud Deploy -->
        <section id="cloud-deploy" class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl space-y-6 scroll-mt-24">
            <div class="space-y-2">
                <span class="text-xs font-bold font-mono text-[#10b981] uppercase tracking-wider">10. Production Hosting</span>
                <h2 class="text-2xl font-bold text-white tracking-tight">Deploying to Render Cloud</h2>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed font-light">
                We provide a pre-configured <code>render.yaml</code> blueprint file for automated SaaS provisioning. To launch your production instance:
            </p>

            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="w-6 h-6 rounded-full bg-[#10b981]/10 border border-[#10b981]/25 flex items-center justify-center text-[10px] font-bold text-[#10b981] font-mono shrink-0">1</div>
                    <div class="space-y-1 text-xs">
                        <strong class="text-white">Link Git Repository:</strong> Go to **Render.com**, navigate to Blueprints, and link the cloned repository.
                    </div>
                </div>
                
                <div class="flex items-start gap-4">
                    <div class="w-6 h-6 rounded-full bg-[#10b981]/10 border border-[#10b981]/25 flex items-center justify-center text-[10px] font-bold text-[#10b981] font-mono shrink-0">2</div>
                    <div class="space-y-1 text-xs">
                        <strong class="text-white">Automatic Provisioning:</strong> Render reads <code>render.yaml</code> to spawn the PostgreSQL cluster, compile the Keras models in Python, and build Laravel Vite assets.
                    </div>
                </div>
            </div>
        </section>

    </main>

</div>
@endsection
