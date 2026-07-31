@extends('layouts.app')
@section('title', 'Account Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your profile, password, and account preferences.</p>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9h2v4h-2V9zm0 6h2v2h-2v-2z" clip-rule="evenodd"/></svg>
        <ul class="text-sm text-red-700 space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- ── Left Sidebar ──────────────────────────────────────────────── --}}
        <div class="md:col-span-1 space-y-4">

            {{-- Profile Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#1B6B3A] to-[#2A8F52] flex items-center justify-center text-white font-black text-xl shadow-md">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-900 leading-tight">{{ $user->name }}</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold mt-1
                            {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : ($user->isLead() ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }} capitalize">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>
                <hr class="border-gray-100 my-3">
                <dl class="space-y-3 text-xs text-gray-600">
                    <div>
                        <dt class="font-medium text-gray-400 uppercase tracking-wider text-[10px]">Email</dt>
                        <dd class="mt-0.5 font-semibold text-gray-800 break-all">{{ $user->email }}</dd>
                    </div>
                    @if($user->designation)
                    <div>
                        <dt class="font-medium text-gray-400 uppercase tracking-wider text-[10px]">Designation</dt>
                        <dd class="mt-0.5 font-semibold text-gray-800">{{ $user->designation }}</dd>
                    </div>
                    @endif
                    @if($user->phone)
                    <div>
                        <dt class="font-medium text-gray-400 uppercase tracking-wider text-[10px]">Phone</dt>
                        <dd class="mt-0.5 font-semibold text-gray-800 font-mono">{{ $user->phone }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="font-medium text-gray-400 uppercase tracking-wider text-[10px]">Member Since</dt>
                        <dd class="mt-0.5 font-semibold text-gray-800">{{ $user->created_at->format('d M Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Privilege Card (role-specific) --}}
            @if($user->isAdmin())
            <div class="bg-purple-50 rounded-xl border border-purple-200 p-4">
                <h3 class="text-xs font-bold text-purple-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.013 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7.013c0-.693.056-1.372.166-2.014zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Administrator
                </h3>
                <ul class="text-xs text-purple-700 space-y-1">
                    <li>✅ Manage all user accounts</li>
                    <li>✅ Create & delete activities</li>
                    <li>✅ View full audit + monitoring</li>
                    <li>✅ Reset any user's password</li>
                    <li>✅ Access all reports</li>
                </ul>
            </div>
            @elseif($user->isLead())
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4">
                <h3 class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-2">Team Lead</h3>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li>✅ Create & edit activities</li>
                    <li>✅ View all reports & monitoring</li>
                    <li>✅ Oversee daily shift board</li>
                    <li>⚠️ Cannot manage user accounts</li>
                </ul>
            </div>
            @else
            <div class="bg-amber-50 rounded-xl border border-amber-200 p-4">
                <h3 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-2">Support Agent</h3>
                <ul class="text-xs text-amber-700 space-y-1">
                    <li>✅ Update activity status & remarks</li>
                    <li>✅ View daily shift board</li>
                    <li>⚠️ Cannot create activities</li>
                    <li>⚠️ Cannot view reports</li>
                </ul>
                <p class="text-[10px] text-amber-600 mt-2 italic">Contact your team lead or admin to change your access level.</p>
            </div>
            @endif

            {{-- Quick links --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Quick Links</h3>
                <nav class="space-y-1">
                    <a href="{{ route('activities.daily') }}" class="flex items-center gap-2 text-sm text-gray-700 hover:text-[#1B6B3A] hover:bg-green-50 px-2 py-1.5 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Today's Board
                    </a>
                    @if($user->canManageActivities())
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-2 text-sm text-gray-700 hover:text-[#1B6B3A] hover:bg-green-50 px-2 py-1.5 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Activity Reports
                    </a>
                    <a href="{{ route('monitoring.index') }}" class="flex items-center gap-2 text-sm text-gray-700 hover:text-[#1B6B3A] hover:bg-green-50 px-2 py-1.5 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        SRE Monitoring
                    </a>
                    @endif
                    @if($user->isAdmin())
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 text-sm text-gray-700 hover:text-[#1B6B3A] hover:bg-green-50 px-2 py-1.5 rounded-lg transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Manage Users
                    </a>
                    @endif
                </nav>
            </div>
        </div>

        {{-- ── Right Forms Area ──────────────────────────────────────────── --}}
        <div class="md:col-span-2 space-y-6">

            {{-- Profile Details Form --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1B6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile Information
                </h2>
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2 outline-none">
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   placeholder="+233 20 000 0000"
                                   class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2 outline-none font-mono">
                        </div>
                        <div>
                            <label for="designation" class="block text-xs font-semibold text-gray-700 mb-1">
                                Designation
                                @if(!$user->canManageActivities())
                                <span class="text-[10px] text-gray-400 font-normal">(read-only)</span>
                                @endif
                            </label>
                            <input type="text" id="designation" name="designation"
                                   value="{{ old('designation', $user->designation) }}"
                                   {{ !$user->canManageActivities() ? 'readonly' : '' }}
                                   placeholder="e.g. System Reliability Engineer"
                                   class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2 outline-none
                                          {{ !$user->canManageActivities() ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : '' }}">
                        </div>
                    </div>

                    {{-- Admin-only: role display (read-only; only super admin can change roles) --}}
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 flex items-center gap-3 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        Your role is <span class="font-bold capitalize text-gray-900 ml-1">{{ $user->role }}</span>.
                        @if($user->isAdmin())
                        <span class="text-xs text-gray-400">Role changes are managed via the Admin → Manage Users panel.</span>
                        @else
                        <span class="text-xs text-gray-400">Contact your administrator to change your role.</span>
                        @endif
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="px-6 py-2 bg-[#1B6B3A] hover:bg-[#12492A] text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Change Password Form --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1B6B3A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Change Password
                </h2>
                <form action="{{ route('settings.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required
                               class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2 outline-none @error('current_password') border-red-400 @enderror">
                        @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold text-gray-700 mb-1">New Password</label>
                            <input type="password" id="password" name="password" required
                                   class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2 outline-none @error('password') border-red-400 @enderror">
                            @error('password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 mb-1">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="block w-full rounded-lg border border-gray-300 bg-white shadow-sm text-sm focus:ring-2 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] px-3 py-2 outline-none">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <p class="text-xs text-gray-400">Minimum 8 characters. Change regularly for security.</p>
                        <button type="submit"
                                class="px-6 py-2 bg-[#1B6B3A] hover:bg-[#12492A] text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Admin Danger Zone --}}
            @if($user->isAdmin())
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
                <h2 class="text-base font-bold text-red-700 border-b border-red-100 pb-3 mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Admin Controls
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Manage Team Accounts</p>
                            <p class="text-xs text-gray-500">Create, edit, reset passwords, or remove team members.</p>
                        </div>
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors flex-shrink-0">
                            Go to Users
                        </a>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Manage Activity Checklist</p>
                            <p class="text-xs text-gray-500">Add, edit, or deactivate shift checklist items.</p>
                        </div>
                        <a href="{{ route('admin.activities.index') }}"
                           class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors flex-shrink-0">
                            Go to Activities
                        </a>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl border border-red-100">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">SRE Monitoring Dashboard</p>
                            <p class="text-xs text-gray-500">Live audit logs, alerts and system health overview.</p>
                        </div>
                        <a href="{{ route('monitoring.index') }}"
                           class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors flex-shrink-0">
                            Open Dashboard
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
