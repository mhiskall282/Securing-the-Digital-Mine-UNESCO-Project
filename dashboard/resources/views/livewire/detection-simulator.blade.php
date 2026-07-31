<div class="space-y-8">
    
    <!-- Hero Header / Explanation Banner -->
    <div class="bg-[#101726] border border-white/5 p-8 rounded-3xl shadow-lg relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-[#F5C518]/5 rounded-full blur-2xl"></div>
        <div class="max-w-3xl space-y-4">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#1B6B3A]/20 text-[#2A8F52] border border-[#1B6B3A]/30">
                Interactive Testing Sandbox
            </span>
            <h2 class="text-3xl font-bold text-white">Intrusion Detection Simulation Sandbox</h2>
            <p class="text-sm text-gray-400 leading-relaxed font-light">
                This sandbox lets you manually inspect and test how our deep learning CNN-LSTM model classifies network connections. By adjusting the 10 features below—which were selected using the Binary Whale Optimization Algorithm (BWOA)—you can see how different parameters trigger threat alerts in real time.
            </p>
        </div>

        <!-- Quick Scenarios Select -->
        <div class="mt-6 border-t border-white/5 pt-6">
            <h4 class="text-xs font-mono text-gray-400 uppercase tracking-wider mb-3">Load Pre-configured Threat Scenarios:</h4>
            <div class="flex flex-wrap gap-3">
                <button wire:click="loadScenario('benign')" class="px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 rounded-xl text-xs font-semibold transition cursor-pointer">
                    🟢 Normal Benign HTTP Traffic
                </button>
                <button wire:click="loadScenario('syn_flood')" class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-xl text-xs font-semibold transition cursor-pointer">
                    🔴 SYN Flood DoS Attack
                </button>
                <button wire:click="loadScenario('port_scan')" class="px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 rounded-xl text-xs font-semibold transition cursor-pointer">
                    🟡 Nmap Port Scan Probe
                </button>
                <button wire:click="loadScenario('privilege_escalation')" class="px-4 py-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-400 border border-purple-500/20 rounded-xl text-xs font-semibold transition cursor-pointer">
                    🔵 Telnet Privilege Escalation (U2R)
                </button>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Feature Configuration -->
        <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg lg:col-span-7 space-y-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#1B6B3A]"></span>
                Adjust Connection Features
            </h3>
            
            <form wire:submit.prevent="analyzePacket" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Protocol Type -->
                <div class="space-y-2">
                    <label class="text-xs font-mono text-gray-300 block">protocol_type</label>
                    <select wire:model.live="protocol_type" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#1B6B3A] focus:outline-none">
                        <option value="tcp">TCP (Standard Connections)</option>
                        <option value="udp">UDP (Streaming/DNS)</option>
                        <option value="icmp">ICMP (Ping/Diagnostic)</option>
                    </select>
                </div>

                <!-- Service -->
                <div class="space-y-2">
                    <label class="text-xs font-mono text-gray-300 block">service</label>
                    <select wire:model.live="service" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#1B6B3A] focus:outline-none">
                        <option value="http">http (Web traffic)</option>
                        <option value="private">private (Private range)</option>
                        <option value="ftp">ftp (File transfer)</option>
                        <option value="smtp">smtp (Mail transfer)</option>
                        <option value="telnet">telnet (Remote shell)</option>
                        <option value="domain">domain (DNS request)</option>
                    </select>
                </div>

                <!-- Flag -->
                <div class="space-y-2">
                    <label class="text-xs font-mono text-gray-300 block">flag</label>
                    <select wire:model.live="flag" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#1B6B3A] focus:outline-none">
                        <option value="SF">SF (Normal transaction)</option>
                        <option value="S0">S0 (SYN connection error)</option>
                        <option value="REJ">REJ (Connection rejected)</option>
                        <option value="RSTR">RSTR (Reset transaction)</option>
                    </select>
                </div>

                <!-- Source Bytes -->
                <div class="space-y-2">
                    <label class="text-xs font-mono text-gray-300 block">src_bytes (Upload data payload size)</label>
                    <input type="number" wire:model.live="src_bytes" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:border-[#1B6B3A] focus:outline-none">
                </div>

                <!-- Hot indicators -->
                <div class="space-y-2">
                    <label class="text-xs font-mono text-gray-300 block">hot (Suspicious activities, e.g. file writes)</label>
                    <input type="number" wire:model.live="hot" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:border-[#1B6B3A] focus:outline-none">
                </div>

                <!-- Superuser attempts -->
                <div class="space-y-2">
                    <label class="text-xs font-mono text-gray-300 block">su_attempted (Admin privileges requested)</label>
                    <select wire:model.live="su_attempted" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#1B6B3A] focus:outline-none">
                        <option value="0">0 (No administrative command)</option>
                        <option value="1">1 (Root shell attempt)</option>
                        <option value="2">2 (Root shell success)</option>
                    </select>
                </div>

                <!-- Serror rate -->
                <div class="space-y-2 md:col-span-2">
                    <div class="flex justify-between text-xs font-mono text-gray-300">
                        <span>serror_rate (Ratio of connections with SYN errors)</span>
                        <span class="text-white font-bold">{{ number_format($serror_rate, 2) }}</span>
                    </div>
                    <input type="range" min="0" max="1" step="0.05" wire:model.live="serror_rate" class="w-full h-1.5 bg-black/45 rounded-lg appearance-none cursor-pointer accent-[#1B6B3A]">
                </div>

                <!-- Same service rate -->
                <div class="space-y-2 md:col-span-2">
                    <div class="flex justify-between text-xs font-mono text-gray-300">
                        <span>same_srv_rate (Ratio of connections to same port/service)</span>
                        <span class="text-white font-bold">{{ number_format($same_srv_rate, 2) }}</span>
                    </div>
                    <input type="range" min="0" max="1" step="0.05" wire:model.live="same_srv_rate" class="w-full h-1.5 bg-black/45 rounded-lg appearance-none cursor-pointer accent-[#1B6B3A]">
                </div>

                <!-- Diff service rate -->
                <div class="space-y-2 md:col-span-2">
                    <div class="flex justify-between text-xs font-mono text-gray-300">
                        <span>diff_srv_rate (Ratio of connections to different ports/services)</span>
                        <span class="text-white font-bold">{{ number_format($diff_srv_rate, 2) }}</span>
                    </div>
                    <input type="range" min="0" max="1" step="0.05" wire:model.live="diff_srv_rate" class="w-full h-1.5 bg-black/45 rounded-lg appearance-none cursor-pointer accent-[#1B6B3A]">
                </div>

                <!-- Destination host diff service rate -->
                <div class="space-y-2 md:col-span-2">
                    <div class="flex justify-between text-xs font-mono text-gray-300">
                        <span>dst_host_diff_srv_rate (Ratio of target hosts receiving different services)</span>
                        <span class="text-white font-bold">{{ number_format($dst_host_diff_srv_rate, 2) }}</span>
                    </div>
                    <input type="range" min="0" max="1" step="0.05" wire:model.live="dst_host_diff_srv_rate" class="w-full h-1.5 bg-black/45 rounded-lg appearance-none cursor-pointer accent-[#1B6B3A]">
                </div>
            </form>
        </div>

        <!-- Right: Classification Outcome -->
        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg space-y-6 sticky top-24">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded-md bg-[#F5C518]"></span>
                    Model Prediction Output
                </h3>

                <!-- Result Status Banner -->
                @if($result)
                    @php
                        $pred = $result['prediction'];
                        $isAnomaly = $pred !== 'Normal';
                    @endphp
                    
                    <div class="p-6 rounded-2xl border text-center transition-all duration-300
                        @if(!$isAnomaly)
                            bg-emerald-500/10 border-emerald-500/20 text-emerald-400
                        @else
                            bg-red-500/10 border-red-500/20 text-red-400
                        @endif">
                        <div class="text-[10px] font-mono uppercase tracking-widest text-gray-400">Classification Result</div>
                        <div class="text-3xl font-extrabold mt-2 font-sans tracking-tight">
                            {{ $pred }}
                        </div>
                        <div class="text-xs text-gray-300 font-mono mt-1">
                            Confidence: <strong class="font-extrabold text-white">{{ $result['confidence'] }}%</strong>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-mono text-gray-400 uppercase tracking-wider">Classification Context</h4>
                        
                        <div class="space-y-2">
                            @foreach($result['reasons'] as $reason)
                                <div class="text-xs text-gray-300 bg-white/[0.02] border border-white/5 p-3 rounded-xl leading-relaxed">
                                    {{ $reason }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Performance Metainfo -->
                    <div class="border-t border-white/5 pt-4 flex justify-between items-center text-[10px] font-mono text-gray-500">
                        <span>Quantized Latency: <strong class="text-white">0.76 ms</strong></span>
                        <span>Processed: {{ $result['timestamp'] }}</span>
                    </div>
                @endif
            </div>

        </div>

    </div>

    <!-- Explainer / Variable glossary -->
    <div class="bg-[#101726] border border-white/5 p-8 rounded-3xl shadow-lg space-y-6">
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="w-1.5 h-6 rounded-md bg-[#F5C518]"></span>
            Connection Features Explanation (Glossary)
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-gray-400 leading-relaxed font-light">
            <div class="space-y-4">
                <div>
                    <strong class="text-white font-mono block mb-1">protocol_type</strong>
                    The networking layer protocol used (TCP for web/control headers, UDP for data streams, ICMP for ping requests).
                </div>
                <div>
                    <strong class="text-white font-mono block mb-1">src_bytes</strong>
                    The total size of data sent from the sender to the target device. Very high numbers might suggest exfiltration or brute forcing.
                </div>
                <div>
                    <strong class="text-white font-mono block mb-1">hot</strong>
                    An indicator tracking dangerous operations (such as trying to write file modifications, access control directories, etc.).
                </div>
                <div>
                    <strong class="text-white font-mono block mb-1">serror_rate</strong>
                    The ratio of requests that generated connection errors. A rate close to 1.0 indicates a flooding attempt (e.g. DoS).
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <strong class="text-white font-mono block mb-1">same_srv_rate</strong>
                    The proportion of connections originating from the same sender addressing the same port/service on the target server.
                </div>
                <div>
                    <strong class="text-white font-mono block mb-1">diff_srv_rate</strong>
                    The proportion of connections originating from the same sender addressing different ports/services.
                </div>
                <div>
                    <strong class="text-white font-mono block mb-1">dst_host_diff_srv_rate</strong>
                    The rate of connections requesting different services specifically targeting the destination host. High diff rates suggest scanning (Probing).
                </div>
                <div>
                    <strong class="text-white font-mono block mb-1">su_attempted</strong>
                    Indicates if a command requested superuser (Administrator) privileges on the target device. Essential for spotting Privilege Escalation (U2R).
                </div>
            </div>
        </div>
    </div>
</div>
