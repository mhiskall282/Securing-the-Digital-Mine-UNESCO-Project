<div>
    @section('title', 'Account Settings & Profile')

    <div class="space-y-8 animate-fade-in max-w-5xl mx-auto">
        <!-- Header -->
        <div class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-[#10b981]/10 text-[#10b981] border border-[#10b981]/25">
                        ACCOUNT CONSOLE
                    </span>
                    <span class="text-xs text-gray-500 font-mono">Org ID: #{{ $organization->id ?? '1' }}</span>
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Account & Organization Settings</h2>
                <p class="text-sm text-gray-400 font-light">Manage your administrator profile, security credentials, and organization metadata.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-black/40 border border-[#2e2e33] rounded-2xl text-right">
                    <div class="text-xs text-gray-400 font-light">Active Devices</div>
                    <div class="text-lg font-bold text-white font-mono">{{ $deviceCount }} Nodes</div>
                </div>
            </div>
        </div>

        @if($status_message)
        <div class="p-4 rounded-2xl bg-[#10b981]/10 border border-[#10b981]/30 text-[#10b981] text-xs font-semibold flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ $status_message }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Profile Info Card -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl shadow-lg space-y-6">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-1.5 h-6 rounded-md bg-[#10b981]"></span>
                        Profile & Organization Metadata
                    </h3>

                    <form wire:submit.prevent="updateProfile" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-mono text-gray-400 mb-1.5 uppercase">Full Name</label>
                                <input type="text" wire:model="name" class="w-full bg-black/40 border border-[#2e2e33] focus:border-[#10b981] rounded-xl px-4 py-2.5 text-xs text-white outline-none transition">
                                @error('name') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-mono text-gray-400 mb-1.5 uppercase">Email Address</label>
                                <input type="email" wire:model="email" class="w-full bg-black/40 border border-[#2e2e33] focus:border-[#10b981] rounded-xl px-4 py-2.5 text-xs text-white outline-none transition">
                                @error('email') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-mono text-gray-400 mb-1.5 uppercase">Organization Name</label>
                                <input type="text" wire:model="org_name" class="w-full bg-black/40 border border-[#2e2e33] focus:border-[#10b981] rounded-xl px-4 py-2.5 text-xs text-white outline-none transition">
                                @error('org_name') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-mono text-gray-400 mb-1.5 uppercase">Job Designation</label>
                                <input type="text" wire:model="designation" placeholder="e.g. Lead Cybersecurity Engineer" class="w-full bg-black/40 border border-[#2e2e33] focus:border-[#10b981] rounded-xl px-4 py-2.5 text-xs text-white outline-none transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-mono text-gray-400 mb-1.5 uppercase">Phone / Contact Number</label>
                            <input type="text" wire:model="phone" placeholder="+1 (555) 000-0000" class="w-full bg-black/40 border border-[#2e2e33] focus:border-[#10b981] rounded-xl px-4 py-2.5 text-xs text-white outline-none transition">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2.5 bg-[#10b981] hover:bg-[#10b981]/90 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-[#10b981]/20 cursor-pointer">
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password Security Card -->
                <div class="bg-[#17171a] border border-[#2e2e33] p-8 rounded-3xl shadow-lg space-y-6">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-1.5 h-6 rounded-md bg-amber-500"></span>
                        Security Credentials & Password
                    </h3>

                    <form wire:submit.prevent="updatePassword" class="space-y-5">
                        <div>
                            <label class="block text-xs font-mono text-gray-400 mb-1.5 uppercase">Current Password</label>
                            <input type="password" wire:model="current_password" class="w-full bg-black/40 border border-[#2e2e33] focus:border-amber-500 rounded-xl px-4 py-2.5 text-xs text-white outline-none transition">
                            @error('current_password') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-mono text-gray-400 mb-1.5 uppercase">New Password</label>
                                <input type="password" wire:model="new_password" class="w-full bg-black/40 border border-[#2e2e33] focus:border-amber-500 rounded-xl px-4 py-2.5 text-xs text-white outline-none transition">
                                @error('new_password') <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-mono text-gray-400 mb-1.5 uppercase">Confirm New Password</label>
                                <input type="password" wire:model="new_password_confirmation" class="w-full bg-black/40 border border-[#2e2e33] focus:border-amber-500 rounded-xl px-4 py-2.5 text-xs text-white outline-none transition">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-500/90 text-black rounded-xl text-xs font-bold transition shadow-lg shadow-amber-500/20 cursor-pointer">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Sidebar: Organization Card -->
            <div class="space-y-6">
                <div class="bg-[#17171a] border border-[#2e2e33] p-6 rounded-3xl shadow-lg space-y-4">
                    <h4 class="text-xs font-mono font-bold uppercase text-gray-400 tracking-wider">Organization Details</h4>
                    
                    <div class="p-4 bg-black/35 border border-[#2e2e33] rounded-2xl space-y-3">
                        <div>
                            <div class="text-[10px] text-gray-500 uppercase font-mono">Organization</div>
                            <div class="text-sm font-bold text-white">{{ $organization->name ?? 'Default Organization' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 uppercase font-mono">Role / Permissions</div>
                            <div class="text-xs text-emerald-400 font-mono font-bold uppercase">{{ $user->role ?? 'admin' }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-500 uppercase font-mono">Joined Date</div>
                            <div class="text-xs text-gray-300 font-mono">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Jul 2026' }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-[#10b981]/10 to-transparent border border-[#2e2e33] p-6 rounded-3xl space-y-3">
                    <h4 class="text-xs font-bold text-white">Need API Support?</h4>
                    <p class="text-xs text-gray-400 font-light leading-relaxed">Refer to the API Documentation or generate new hardware tokens under Device Nodes.</p>
                    <a href="{{ route('api-docs') }}" class="inline-block text-xs font-bold text-[#10b981] hover:underline font-mono">Explore API Portal &rarr;</a>
                </div>
            </div>

        </div>
    </div>
</div>
