<div wire:poll.2s="autoSniffTick" class="space-y-8">
    
    <!-- Top Header -->
    <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                Real-Time Traffic Monitor
            </h2>
            <p class="text-sm text-gray-400 font-light mt-1">Sniffing local adapters and classification stream. Updating live every second.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="toggleStreaming" class="px-4 py-2 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-2 border <?php if($isAutoSniffing): ?> bg-emerald-500/10 text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/20 <?php else: ?> bg-amber-500/10 text-amber-400 border-amber-500/30 hover:bg-amber-500/20 <?php endif; ?>">
                <span class="relative flex h-2 w-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAutoSniffing): ?>
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    <?php else: ?>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <?php echo e($isAutoSniffing ? 'Streaming Active (Pause)' : 'Streaming Paused (Resume)'); ?>

            </button>

            <button wire:click="triggerIntermittentSniffer" class="px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Inject Burst (5 Packets)
            </button>

            <button wire:click="clearLogs" class="px-4 py-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-xl text-xs font-semibold transition cursor-pointer">
                Clear Log History
            </button>
        </div>
    </div>

    <!-- Active Grid Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Flows Evaluated</div>
            <div class="mt-2 text-3xl font-extrabold text-white"><?php echo e(number_format($totalFlows)); ?></div>
            <div class="text-[10px] text-gray-500 mt-1 font-mono">Real-time packet logs</div>
        </div>

        <!-- Card 2 -->
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Benign (Normal)</div>
            <div class="mt-2 text-3xl font-extrabold text-emerald-400"><?php echo e(number_format($normalCount)); ?></div>
            <div class="text-[10px] text-emerald-600 mt-1 font-mono">Secured network packets</div>
        </div>

        <!-- Card 3 -->
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Anomalies Detected</div>
            <div class="mt-2 text-3xl font-extrabold text-red-400"><?php echo e(number_format($attackCount)); ?></div>
            <div class="text-[10px] text-red-600 mt-1 font-mono">Mitigated threat flows</div>
        </div>

        <!-- Card 4 -->
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl relative overflow-hidden">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($anomalyRate > 0): ?>
                <div class="absolute right-0 bottom-0 translate-x-4 translate-y-4 w-12 h-12 bg-red-500/10 rounded-full blur-xl animate-pulse"></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="text-xs text-gray-400 font-mono uppercase">Anomalous Ratio</div>
            <div class="mt-2 text-3xl font-extrabold <?php if($anomalyRate > 0): ?> text-red-400 <?php else: ?> text-emerald-400 <?php endif; ?>"><?php echo e($anomalyRate); ?>%</div>
            <div class="text-[10px] text-gray-500 mt-1 font-mono">Active threat concentration</div>
        </div>
    </div>

    <!-- Distribution and Ticker -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Class Distribution bars -->
        <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg lg:col-span-4 space-y-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#F5C518]"></span>
                Threat Distribution
            </h3>

            <div class="space-y-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $classDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $percentage = $totalFlows > 0 ? ($count / $totalFlows) * 100 : 0;
                        $barColor = $label === 'Normal' ? 'bg-emerald-500' : ($label === 'DoS' ? 'bg-red-500' : ($label === 'Probe' ? 'bg-amber-500' : 'bg-purple-500'));
                    ?>
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="font-mono text-gray-300"><?php echo e($label); ?></span>
                            <span class="text-gray-400 font-mono"><?php echo e($count); ?> (<?php echo e(round($percentage)); ?>%)</span>
                        </div>
                        <div class="w-full bg-black/40 h-2 rounded-full overflow-hidden">
                            <div class="<?php echo e($barColor); ?> h-2 rounded-full" style="width: <?php echo e($percentage); ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <!-- Right: Real-time Live Ticker -->
        <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg lg:col-span-8 space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#1B6B3A]"></span>
                Live Packet Traffic Feed
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 text-gray-500 font-mono text-[10px] uppercase tracking-wider">
                            <th class="pb-2">Time</th>
                            <th class="pb-2">Proto</th>
                            <th class="pb-2">Service</th>
                            <th class="pb-2">Flag</th>
                            <th class="pb-2 text-right">Size</th>
                            <th class="pb-2 text-center">Prediction</th>
                            <th class="pb-2 text-right">Confidence</th>
                            <th class="pb-2 text-right pr-2">Latency</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs text-gray-300 font-mono">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $latestFlows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $flow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $isAnomaly = $flow->prediction !== 'Normal';
                            ?>
                            <tr class="hover:bg-white/[0.01]">
                                <td class="py-2.5 text-gray-500"><?php echo e(date('H:i:s', strtotime($flow->timestamp))); ?></td>
                                <td class="py-2.5">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white/5 text-cyan-400"><?php echo e(strtoupper($flow->protocol_type)); ?></span>
                                </td>
                                <td class="py-2.5 text-gray-400"><?php echo e($flow->service); ?></td>
                                <td class="py-2.5 text-gray-400"><?php echo e($flow->flag); ?></td>
                                <td class="py-2.5 text-right"><?php echo e(number_format($flow->src_bytes)); ?> B</td>
                                <td class="py-2.5 text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isAnomaly): ?>
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25">Benign</span>
                                    <?php else: ?>
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-red-500/10 text-red-400 border border-red-500/25"><?php echo e($flow->prediction); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="py-2.5 text-right font-semibold <?php if($isAnomaly): ?> text-red-300 <?php else: ?> text-emerald-300 <?php endif; ?>"><?php echo e(number_format($flow->confidence, 2)); ?>%</td>
                                <td class="py-2.5 text-right pr-2 text-gray-400"><?php echo e(number_format($flow->latency_ms, 2)); ?> ms</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-8 text-gray-500">
                                    No live traffic detected. Run the <code class="bg-black/35 px-1 py-0.5 rounded text-[#F5C518]">sniffer_daemon.py</code> or launch the NPM CLI tool to stream data.
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Metric Explanations & Scientific Diagnostics -->
        <div class="bg-[#101726] border border-white/5 p-8 rounded-3xl shadow-lg space-y-6">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#10b981]"></span>
                Telemetry Metric Breakdown & Diagnostic Guide
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-black/35 border border-[#2e2e33] p-5 rounded-2xl space-y-2">
                    <div class="text-xs font-mono font-bold text-[#10b981] uppercase">1. Evaluated Flows & Latency</div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">
                        Measures raw packet headers evaluated per second. Sub-millisecond latency (&lt;0.8ms) ensures low-power Raspberry Pi edge gateways process inline SCADA traffic without inducing control loop delays.
                    </p>
                </div>

                <div class="bg-black/35 border border-[#2e2e33] p-5 rounded-2xl space-y-2">
                    <div class="text-xs font-mono font-bold text-amber-400 uppercase">2. BWOA Feature Dimensionality</div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">
                        Raw flow records contain 41 KDD/NSL metrics. BWOA prunes non-informative variables down to 10 selected features (e.g. <code>serror_rate</code>, <code>same_srv_rate</code>, <code>hot</code>), reducing CPU overhead by 75.6%.
                    </p>
                </div>

                <div class="bg-black/35 border border-[#2e2e33] p-5 rounded-2xl space-y-2">
                    <div class="text-xs font-mono font-bold text-purple-400 uppercase">3. Softmax Confidence %</div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">
                        Probability output from the final Dense layer of the CNN-LSTM deep neural network. Classifications exceeding 90% trigger automated alert dispatching to central monitoring servers.
                    </p>
                </div>
            </div>
        </div>

        <!-- Actionable Threat Mitigation Playbooks -->
        <div class="bg-[#101726] border border-white/5 p-8 rounded-3xl shadow-lg space-y-6">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-red-500"></span>
                Actionable Threat Mitigation Playbooks & Automated Countermeasures
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Playbook 1: DoS -->
                <div class="bg-black/35 border border-red-500/20 p-6 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-red-400 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            Denial of Service (DoS / DDoS)
                        </span>
                        <span class="text-[10px] font-mono bg-red-500/10 text-red-400 border border-red-500/20 px-2 py-0.5 rounded">High Severity</span>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">
                        High syn-error rates (<code>serror_rate &gt; 0.70</code>) flooding TCP ports to exhaust microcontroller buffer queues.
                    </p>
                    <div class="space-y-1.5 pt-1">
                        <div class="text-[11px] font-bold text-white font-mono uppercase">Automated Mitigation Steps:</div>
                        <ul class="text-xs text-gray-300 space-y-1 list-disc list-inside font-mono text-[11px]">
                            <li>Apply kernel-level IP table rate limiting (<code>iptables -A INPUT -p tcp --syn -m limit --limit 1/s -j ACCEPT</code>).</li>
                            <li>Isolate flooded Modbus/DNP3 industrial switch ports using 802.1X VLAN segregation.</li>
                            <li>Broadcast BGP FlowSpec rules to upstream ISP scrubbers.</li>
                        </ul>
                    </div>
                </div>

                <!-- Playbook 2: Probe -->
                <div class="bg-black/35 border border-amber-500/20 p-6 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-amber-400 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Reconnaissance (Probe / Port Scan)
                        </span>
                        <span class="text-[10px] font-mono bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded">Medium Severity</span>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">
                        ICMP and private service probing aimed at discovering active IP ranges and exposed SCADA endpoints.
                    </p>
                    <div class="space-y-1.5 pt-1">
                        <div class="text-[11px] font-bold text-white font-mono uppercase">Automated Mitigation Steps:</div>
                        <ul class="text-xs text-gray-300 space-y-1 list-disc list-inside font-mono text-[11px]">
                            <li>Enable dynamic firewall blacklisting via Fail2ban rule triggers.</li>
                            <li>Reroute probe packets to an isolated Honeypot container logging attacker signatures.</li>
                            <li>Disable ICMP echo-reply responses across OT subnet interfaces.</li>
                        </ul>
                    </div>
                </div>

                <!-- Playbook 3: U2R -->
                <div class="bg-black/35 border border-purple-500/20 p-6 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-purple-400 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                            User to Root (U2R Privilege Escalation)
                        </span>
                        <span class="text-[10px] font-mono bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2 py-0.5 rounded">Critical Severity</span>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">
                        Buffer overflow or local exploit attempts (<code>hot &gt; 0</code>, <code>su_attempted &gt; 0</code>) seeking root privileges.
                    </p>
                    <div class="space-y-1.5 pt-1">
                        <div class="text-[11px] font-bold text-white font-mono uppercase">Automated Mitigation Steps:</div>
                        <ul class="text-xs text-gray-300 space-y-1 list-disc list-inside font-mono text-[11px]">
                            <li>Immediately terminate compromised SSH/TTY user sessions (<code>pkill -KILL -u username</code>).</li>
                            <li>Trigger Pluggable Authentication Module (PAM) credential rotation.</li>
                            <li>Quarantine physical edge node and initiate forensic memory dump.</li>
                        </ul>
                    </div>
                </div>

                <!-- Playbook 4: R2L -->
                <div class="bg-black/35 border border-blue-500/20 p-6 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-blue-400 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            Remote to Local (R2L Compromise)
                        </span>
                        <span class="text-[10px] font-mono bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2 py-0.5 rounded">High Severity</span>
                    </div>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">
                        Unauthorized access attempts from external IP streams into local mine network telemetry channels.
                    </p>
                    <div class="space-y-1.5 pt-1">
                        <div class="text-[11px] font-bold text-white font-mono uppercase">Automated Mitigation Steps:</div>
                        <ul class="text-xs text-gray-300 space-y-1 list-disc list-inside font-mono text-[11px]">
                            <li>Enforce Multi-Factor Authentication (MFA) challenge on all external gateway ingress ports.</li>
                            <li>Revoke TLS client certificates for suspect IP addresses.</li>
                            <li>Block unencrypted Telnet/FTP ports and force TLS 1.3 encryption.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

</div>
<?php /**PATH C:\Users\user\Desktop\unesco-project\dashboard\resources\views/livewire/live-monitor.blade.php ENDPATH**/ ?>