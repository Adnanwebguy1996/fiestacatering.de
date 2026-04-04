<div class="bg-white min-h-screen">
    <!-- 1. Header Banner -->
    <section class="h-64 relative overflow-hidden flex items-center justify-center p-0">
        <div class="absolute inset-0 flex">
            @foreach(['hero1.webp', 'hero2.webp', 'hero3.webp', 'hero4.webp'] as $img)
            <div class="flex-1">
                <img src="/images/{{ $img }}" class="w-full h-full object-cover">
            </div>
            @endforeach
        </div>
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 text-center text-white">
            <h1 class="text-7xl font-snugle mb-2 italic">FAQ'S</h1>
            <div class="flex items-center justify-center space-x-2 text-xl font-snugle text-[#f57c00]">
                <span>Home</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-white">FAQs</span>
            </div>
        </div>
    </section>

    <!-- 2. FAQ Content -->
    <section class="max-w-4xl mx-auto px-4 py-24 text-center">
        <h2 class="text-5xl font-snugle text-[#1a130c] mb-12 italic">FAQs</h2>

        <!-- Role Toggle -->
        <div class="flex items-center justify-center mb-16">
            <div class="bg-[#fff4eb] border border-gray-100 rounded-full p-1.5 flex shadow-inner">
                <button class="text-gray-400 px-10 py-3 rounded-full font-bold hover:text-gray-600 transition-colors">Customers</button>
                <button class="bg-[#f57c00] text-white px-10 py-3 rounded-full font-bold shadow-lg">Caterers</button>
            </div>
        </div>

        <!-- Accordion (Alpine.js powered) -->
        <div x-data="{ active: 1 }" class="space-y-6 text-left">
            @foreach([
                1 => '1. How can I register my food truck with Fiesta Catering?',
                2 => '2. What advantages does the platform offer me?',
                3 => '3. How is billing handled?',
                4 => '4. Can I edit or pause my profile at any time?',
                5 => '5. How do I deal with cancelations?'
            ] as $id => $question)
            <div class="bg-[#f9f9f9] border border-gray-100 rounded-3xl overflow-hidden transition-all duration-300" 
                 :class="active === {{ $id }} ? 'shadow-lg border-[#f57c00]' : ''">
                <button @click="active = active === {{ $id }} ? null : {{ $id }}" 
                        class="w-full flex items-center justify-between p-8 group">
                    <span class="text-xl font-bold text-gray-800 group-hover:text-[#f57c00] transition-colors italic">{{ $question }}</span>
                    <div class="bg-gray-100 p-2 rounded-lg group-hover:bg-orange-50" :class="active === {{ $id }} ? 'rotate-180 bg-orange-50' : ''">
                        <svg class="h-6 w-6 text-[#1a130c]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
                <div x-show="active === {{ $id }}" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-8 pb-8 text-gray-500 italic leading-relaxed">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>
