@extends('layouts.app')
@section('title', 'Manage Team Members')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Team Members</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $users->total() }} member(s) registered</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="px-4 py-2 bg-[#1B6B3A] hover:bg-[#2A8F52] text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Member
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-200 rounded-xl">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('warning'))
    <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <p class="text-sm font-semibold text-amber-800">{{ session('warning') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-xl">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9h2v4h-2V9zm0 6h2v2h-2v-2z" clip-rule="evenodd"/></svg>
        <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Users Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Designation</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="relative px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @foreach($users as $member)
                <tr class="hover:bg-green-50/30 transition-colors duration-100">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-50 border border-green-100 flex items-center justify-center text-[#1B6B3A] font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $member->name }}</p>
                                <p class="text-xs text-gray-400">{{ $member->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="capitalize text-xs px-2.5 py-1 rounded-full font-semibold
                            {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-700' :
                               ($member->role === 'lead'  ? 'bg-blue-100 text-blue-700'   : 'bg-gray-100 text-gray-600') }}">
                            {{ $member->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $member->designation ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-500 text-xs font-mono">{{ $member->phone ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $member->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2 flex-wrap">

                            {{-- Edit --}}
                            <a href="{{ route('admin.users.edit', $member) }}"
                               class="px-3 py-1 text-xs font-semibold text-[#1B6B3A] bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                Edit
                            </a>

                            {{-- Reset Password (not for yourself to avoid lockout confusion) --}}
                            @if(auth()->id() !== $member->id)
                            <form action="{{ route('admin.users.resetPassword', $member) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Send a password-reset email to {{ $member->name }}?\nThey will receive a secure link valid for 60 minutes.')">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                                    🔑 Reset Password
                                </button>
                            </form>
                            @else
                            <a href="{{ route('settings.edit') }}"
                               class="px-3 py-1 text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                My Settings
                            </a>
                            @endif

                            {{-- Remove (can't delete yourself) --}}
                            @if(auth()->id() !== $member->id)
                            <form action="{{ route('admin.users.destroy', $member) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Remove {{ $member->name }} permanently? This action cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                    Remove
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Legend / instructions --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-700 space-y-1">
        <p class="font-semibold text-blue-800 mb-1">About password resets:</p>
        <p>🔑 Clicking <strong>Reset Password</strong> sends a secure, time-limited link (60 min) to the user's email — you never see or set the new password.</p>
        <p>📧 New members automatically receive a welcome email with their credentials when their account is created.</p>
    </div>

</div>
@endsection
