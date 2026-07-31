@extends('layouts.app')
@section('title', 'New Activity')
@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create Activity</h1>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.activities.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-[#E63946]">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] @error('title') border-[#E63946] @enderror">
                @error('title')<p class="mt-1 text-xs text-[#E63946]">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
                        <option value="">— Select —</option>
                        @foreach(['Application','Infrastructure','Database','Network','Security'] as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recurrence</label>
                    <select name="recurrence" class="block w-full rounded-lg border border-gray-300 text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
                        <option value="daily">Daily</option>
                        <option value="adhoc">Ad Hoc</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" id="is_active" class="rounded border-gray-300 text-[#1B6B3A] focus:ring-[#1B6B3A]" checked>
                <label for="is_active" class="text-sm text-gray-700">Active</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-[#1B6B3A] hover:bg-[#2A8F52] text-white text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm">Create</button>
                <a href="{{ route('admin.activities.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-150">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
