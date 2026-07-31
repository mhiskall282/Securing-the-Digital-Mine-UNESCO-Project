<div class="space-y-8">
    
    <!-- Top Header -->
    <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg">
        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
            <span class="p-1.5 rounded-lg bg-[#10b981]/15 text-[#10b981]">🛡️</span>
            Master Administrator Panel
        </h2>
        <p class="text-sm text-gray-400 font-light mt-1">Global administrative hub to manage SaaS tenant accounts, registered devices, and system authorizations.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Total Tenants</div>
            <div class="mt-2 text-3xl font-extrabold text-white">{{ $organizations->count() }}</div>
        </div>
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Total Users</div>
            <div class="mt-2 text-3xl font-extrabold text-[#10b981]">{{ $users->count() }}</div>
        </div>
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Connected Device Nodes</div>
            <div class="mt-2 text-3xl font-extrabold text-white">{{ $devices->count() }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left: Organizations list -->
        <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#10b981]"></span>
                Registered Organizations (Tenants)
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 text-gray-500 font-mono text-[10px] uppercase">
                            <th class="pb-2">ID</th>
                            <th class="pb-2">Organization</th>
                            <th class="pb-2 text-center">Users</th>
                            <th class="pb-2 text-center">Devices</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs text-gray-300 font-mono">
                        @forelse($organizations as $org)
                            <tr class="hover:bg-white/[0.01]">
                                <td class="py-2.5 text-gray-500">#{{ $org->id }}</td>
                                <td class="py-2.5 font-semibold text-white">{{ $org->name }}</td>
                                <td class="py-2.5 text-center">{{ $org->users_count }}</td>
                                <td class="py-2.5 text-center">{{ $org->devices_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-500">No organizations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Device Nodes list -->
        <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#10b981]"></span>
                Active Global Device Nodes
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 text-gray-500 font-mono text-[10px] uppercase">
                            <th class="pb-2">Node Name</th>
                            <th class="pb-2">Tenant Organization</th>
                            <th class="pb-2">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs text-gray-300 font-mono">
                        @forelse($devices as $dev)
                            <tr class="hover:bg-white/[0.01]">
                                <td class="py-2.5 font-semibold text-white">{{ $dev->name }}</td>
                                <td class="py-2.5 text-gray-400">{{ $dev->organization->name ?? 'N/A' }}</td>
                                <td class="py-2.5 text-gray-500">{{ $dev->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray-500">No devices registered globally.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom: User Access Control -->
        <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg lg:col-span-2 space-y-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-1.5 h-6 rounded-md bg-[#10b981]"></span>
                Global User Directory & Authorizations
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 text-gray-500 font-mono text-[10px] uppercase">
                            <th class="pb-2">User Name</th>
                            <th class="pb-2">Email Address</th>
                            <th class="pb-2">Organization</th>
                            <th class="pb-2 text-center">Master Admin</th>
                            <th class="pb-2 text-right pr-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs text-gray-300 font-mono">
                        @foreach($users as $user)
                            <tr class="hover:bg-white/[0.01]">
                                <td class="py-3 font-semibold text-white">{{ $user->name }}</td>
                                <td class="py-3 text-gray-400">{{ $user->email }}</td>
                                <td class="py-3 text-gray-400">{{ $user->organization->name ?? 'N/A' }}</td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold @if($user->is_admin) bg-emerald-500/10 text-emerald-400 @else bg-gray-500/10 text-gray-500 @endif">
                                        {{ $user->is_admin ? 'YES' : 'NO' }}
                                    </span>
                                </td>
                                <td class="py-3 text-right pr-2 space-x-2">
                                    <button wire:click="toggleUserAdmin({{ $user->id }})" class="px-2.5 py-1 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-lg text-[10px] font-semibold transition cursor-pointer">
                                        Toggle Admin
                                    </button>
                                    @if(auth()->id() !== $user->id)
                                        <button wire:click="deleteUser({{ $user->id }})" class="px-2.5 py-1 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-lg text-[10px] font-semibold transition cursor-pointer">
                                            Delete User
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
