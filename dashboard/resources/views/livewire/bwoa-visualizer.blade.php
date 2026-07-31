<div class="space-y-8">
    
    <!-- Header -->
    <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">BWOA Feature Selection Visualizer</h2>
            <p class="text-sm text-gray-400 font-light mt-1">Interactive step-through of the Binary Whale Optimization Algorithm v3 feature pruning.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="resetSimulation" class="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                Reset
            </button>
            <button wire:click="nextStep" @if($isConverged) disabled @endif class="px-4 py-2 bg-[#1B6B3A] hover:bg-[#2A8F52] disabled:opacity-40 text-white rounded-xl text-xs font-semibold transition cursor-pointer">
                Next Iteration
            </button>
            <button wire:click="fastForward" @if($isConverged) disabled @endif class="px-4 py-2 bg-[#F5C518] hover:bg-[#E8A500] disabled:opacity-40 text-black rounded-xl text-xs font-semibold transition cursor-pointer">
                Run to Convergence
            </button>
        </div>
    </div>

    <!-- Status Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        
        <!-- Iteration -->
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Iteration</div>
            <div class="mt-2 text-3xl font-extrabold text-white">{{ $iteration }} <span class="text-xs text-gray-500 font-normal">/ 100 max</span></div>
        </div>

        <!-- Fitness -->
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Current Fitness</div>
            <div class="mt-2 text-3xl font-extrabold text-emerald-400">{{ number_format($fitness, 4) }}</div>
        </div>

        <!-- Selected Count -->
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Features Selected</div>
            <div class="mt-2 text-3xl font-extrabold text-white">
                {{ count($selectedFeatures) }} <span class="text-xs text-gray-500 font-normal">/ 41 total</span>
            </div>
        </div>

        <!-- Current Phase -->
        <div class="bg-[#101726] border border-white/5 p-5 rounded-2xl">
            <div class="text-xs text-gray-400 font-mono uppercase">Current Phase</div>
            <div class="mt-2 text-sm font-semibold text-[#F5C518] truncate">{{ $currentPhase }}</div>
        </div>
    </div>

    <!-- Main Visual grid of features -->
    <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg space-y-6">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <span class="w-1.5 h-6 rounded-md bg-[#1B6B3A]"></span>
            NSL-KDD Feature Mask Matrix
        </h3>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($allFeatures as $feature)
                @php
                    $isSelected = inis_array_check($feature, $selectedFeatures);
                    $isFinal = inis_array_check($feature, $finalFeatures);
                @endphp
                <div class="p-3 rounded-xl border text-xs font-mono flex flex-col justify-between h-20 transition-all duration-200 
                    @if($isSelected) 
                        @if($isConverged && $isFinal)
                            bg-emerald-500/10 border-emerald-500/40 text-emerald-400 shadow-md shadow-emerald-950/20
                        @else
                            bg-blue-500/10 border-blue-500/40 text-blue-300
                        @endif
                    @else
                        bg-[#070a13]/40 border-white/5 text-gray-600
                    @endif">
                    <span class="truncate font-semibold">{{ $feature }}</span>
                    
                    <div class="flex items-center justify-between mt-2">
                        @if($isSelected)
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-sans font-semibold">Active</span>
                        @else
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-white/5 text-gray-500 font-sans">Dropped</span>
                        @endif

                        @if($isConverged && $isFinal)
                            <span class="text-[9px] text-[#F5C518] font-sans font-bold">★ Optimal</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Explanation Box -->
    <div class="bg-[#101726] border border-white/5 p-6 rounded-3xl shadow-lg space-y-4">
        <h3 class="text-lg font-bold text-white">Whale Optimization Algorithm (WOA) Concept</h3>
        <p class="text-sm text-gray-400 leading-relaxed font-light">
            In BWOA, each feature subset is represented by a binary vector (where 1 indicates the feature is selected). Whales search for food by updating their positions relative to the leader (best feature subset found so far).
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
            <div class="space-y-1">
                <h4 class="font-bold text-white text-sm">1. Encircling Prey</h4>
                <p class="text-xs text-gray-400 font-light">Whales encircle target feature subsets by updating positions based on the leader's coordinates.</p>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-[#F5C518] text-sm">2. Bubble-net Attack</h4>
                <p class="text-xs text-gray-400 font-light">Simulates spiral movements to search. Transitions between encircling and spiral configurations dynamically.</p>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-white text-sm">3. V-shaped Transfer Function</h4>
                <p class="text-xs text-gray-400 font-light">Converts continuous position values to binary format to select/deselect features probabilistically.</p>
            </div>
        </div>
    </div>
</div>

@php
    function inis_array_check($needle, $haystack) {
        return in_array($needle, $haystack, true);
    }
@endphp
