<div>
    @if($showForm)
        <div class="min-w-[220px] bg-white border border-gray-200 rounded-lg shadow-md p-3 space-y-2"
             x-data x-trap="true">
            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Update Status</p>

            <div class="flex gap-2">
                <button wire:click="$set('status', 'pending')"
                        class="flex-1 py-1 text-xs font-medium rounded-md border transition-colors duration-100
                               {{ $status === 'pending' ? 'bg-[#F5C518] border-[#F5C518] text-gray-900' : 'border-gray-300 text-gray-600 hover:border-[#F5C518]' }}">
                    Pending
                </button>
                <button wire:click="$set('status', 'done')"
                        class="flex-1 py-1 text-xs font-medium rounded-md border transition-colors duration-100
                               {{ $status === 'done' ? 'bg-[#1B6B3A] border-[#1B6B3A] text-white' : 'border-gray-300 text-gray-600 hover:border-[#1B6B3A]' }}">
                    Done
                </button>
            </div>

            <textarea wire:model="remark"
                      placeholder="Add a remark (optional)..."
                      rows="2"
                      class="w-full text-xs border border-gray-300 rounded-md p-2 focus:ring-1 focus:ring-[#1B6B3A] focus:border-[#1B6B3A] resize-none">
            </textarea>

            @error('remark')
                <p class="text-xs text-[#E63946]">{{ $message }}</p>
            @enderror

            <div class="flex gap-2">
                <button wire:click="save"
                        wire:loading.attr="disabled"
                        class="flex-1 py-1.5 text-xs font-semibold bg-[#1B6B3A] text-white rounded-md
                               hover:bg-[#2A8F52] transition-colors duration-150 disabled:opacity-50">
                    <span wire:loading.remove>Save</span>
                    <span wire:loading>Saving...</span>
                </button>
                <button wire:click="toggleForm"
                        class="px-3 py-1.5 text-xs text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-150">
                    Cancel
                </button>
            </div>
        </div>
    @else
        <button wire:click="toggleForm"
                class="px-3 py-1.5 text-xs font-medium border border-[#1B6B3A] text-[#1B6B3A] rounded-md
                       hover:bg-[#1B6B3A] hover:text-white transition-colors duration-150">
            Update
        </button>
    @endif
</div>
