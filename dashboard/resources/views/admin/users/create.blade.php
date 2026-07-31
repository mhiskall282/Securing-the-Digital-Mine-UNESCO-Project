@extends('layouts.app')
@section('title', 'Add Team Member')
@section('content')
<div class="max-w-lg">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Add Team Member</h1>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-[#E63946]">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] @error('name') border-[#E63946] @enderror">
                @error('name')<p class="mt-1 text-xs text-[#E63946]">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-[#E63946]">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] @error('email') border-[#E63946] @enderror">
                @error('email')<p class="mt-1 text-xs text-[#E63946]">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-[#E63946]">*</span></label>
                    <select name="role" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
                        <option value="agent">Agent</option>
                        <option value="lead">Lead</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Support Engineer" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-[#E63946]">*</span></label>
                <input type="password" name="password" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] @error('password') border-[#E63946] @enderror">
                @error('password')<p class="mt-1 text-xs text-[#E63946]">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-[#E63946]">*</span></label>
                <input type="password" name="password_confirmation" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-[#1B6B3A] hover:bg-[#2A8F52] text-white text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm">
                    Create Member
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-150">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
