@extends('layouts.app')
@section('title', 'Edit Team Member')
@section('content')
<div class="max-w-lg">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit: {{ $user->name }}</h1>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
                        @foreach(['agent','lead','admin'] as $role)
                        <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-[#1B6B3A] hover:bg-[#2A8F52] text-white text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm">
                    Save Changes
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-150">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
