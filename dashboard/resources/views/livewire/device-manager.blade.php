<div class="space-y-8">
    
    <!-- Top Header -->
    <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                <span class="p-1.5 rounded-lg bg-[#10b981]/15 text-[#10b981]">🔌</span>
                Device Node Manager
            </h2>
            <p class="text-sm text-gray-400 font-light mt-1">Register and provision credentials for your IoT collectors and network sniffing nodes.</p>
        </div>
    </div>

    <!-- Alert Message for Token Copy -->
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs rounded-2xl flex flex-col gap-2 relative">
            <strong>⚠️ IMPORTANT: Copy your API Token now. It will not be shown again!</strong>
            <div class="bg-black/40 p-3 rounded-xl font-mono select-all text-white border border-white/5">
                {{ str_replace('Device registered successfully. Copy your API token: ', '', session('message')) }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Register Device Form -->
        <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg lg:col-span-5 space-y-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#10b981]"></span>
                Register New Sensor Node
            </h3>

            <form wire:submit.prevent="registerDevice" class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-300">Device/Node Name</label>
                    <input type="text" wire:model="name" required class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:border-[#10b981] focus:outline-none placeholder-gray-600" placeholder="e.g. Raspberry Pi Mine Shaft 3">
                </div>

                <button type="submit" class="px-5 py-2.5 bg-[#10b981] hover:bg-[#0da672] text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                    Generate Access Key
                </button>
            </form>
        </div>

        <!-- Right: Registered Devices List -->
        <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg lg:col-span-7 space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#10b981]"></span>
                Active Registered Nodes
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 text-gray-500 font-mono text-[10px] uppercase tracking-wider">
                            <th class="pb-2">Node Name</th>
                            <th class="pb-2">API Token Hash</th>
                            <th class="pb-2">Registered</th>
                            <th class="pb-2 text-right pr-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs text-gray-300 font-mono">
                        @forelse($devices as $device)
                            <tr class="hover:bg-white/[0.01]">
                                <td class="py-3 font-semibold text-white">{{ $device->name }}</td>
                                <td class="py-3 text-gray-400">
                                    {{ substr($device->api_token, 0, 15) }}...[Obscured]
                                </td>
                                <td class="py-3 text-gray-500">{{ $device->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3 text-right pr-2">
                                    <button wire:click="deleteDevice({{ $device->id }})" class="px-2.5 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-lg text-[10px] font-semibold transition cursor-pointer">
                                        Revoke Node
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-500">
                                    No registered nodes found. Register a device node above to receive authorization credentials.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
