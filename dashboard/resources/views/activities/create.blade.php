@extends('layouts.app')
@section('title', 'New Activity')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('activities.index') }}" class="text-gray-400 hover:text-[#1B6B3A] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Create Activity</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('activities.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-[#E63946]">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}"
                       class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A] @error('title') border-[#E63946] @enderror"
                       placeholder="e.g. Daily SMS count vs SMS count from logs">
                @error('title')<p class="mt-1 text-xs text-[#E63946]">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]"
                          placeholder="What does this activity check? What constitutes a pass?">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category" name="category"
                            class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
                        <option value="">— Select —</option>
                        @foreach(['Application','Infrastructure','Database','Network','Security'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="recurrence" class="block text-sm font-medium text-gray-700 mb-1">Recurrence <span class="text-[#E63946]">*</span></label>
                    <select id="recurrence" name="recurrence"
                            class="block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-[#1B6B3A] focus:border-[#1B6B3A]">
                        <option value="daily" {{ old('recurrence', 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="adhoc" {{ old('recurrence') === 'adhoc' ? 'selected' : '' }}>Ad Hoc</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       class="rounded border-gray-300 text-[#1B6B3A] focus:ring-[#1B6B3A]"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">Active (appears on daily board)</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 bg-[#1B6B3A] hover:bg-[#2A8F52] text-white text-sm font-semibold
                               rounded-lg transition-colors duration-150 shadow-sm">
                    Create Activity
                </button>
                <a href="{{ route('activities.index') }}"
                   class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium
                          rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
