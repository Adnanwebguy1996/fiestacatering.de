<div class="bg-white min-h-screen">
    <!-- 1. Header Banner -->
    <section class="h-64 relative overflow-hidden flex items-center justify-center p-0">
        <div class="absolute inset-0 flex">
            @foreach(['hero1.webp', 'hero2.webp', 'hero3.webp', 'hero4.webp'] as $img)
            <div class="flex-1">
                <img src="/images/home/{{ $img }}" class="w-full h-full object-cover">
            </div>
            @endforeach
        </div>
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 text-center text-white">
            <h1 class="text-6xl font-snugle mb-2 italic">Fiesta Catering</h1>
            <div class="flex items-center justify-center space-x-2 text-xl font-snugle text-[#f57c00]">
                <span>Home</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-white">About Us</span>
            </div>
        </div>
    </section>

    <!-- 2. Introduction Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div class="space-y-8">
                <div class="w-20 h-1 bg-[#f57c00]"></div>
                <p class="text-gray-600 text-lg leading-relaxed italic">
                    Fiesta Catering – We are the event professionals with taste! Our heart beats for unique experiences and delicious moments that simply taste like "more". Whether it's crispy tacos, creamy ice cream or freshly brewed coffee - we ensure that every event becomes a culinary adventure! As an experienced agency, we know the best food trucks and caterers to take celebrations to a new level, from casual company events to the dream wedding. Our motto: The more colorful, the better, the tastier, the more fiesta! Let's make your event a celebration of the year together - with heart, humor and lots of taste!
                </p>
            </div>
            <div class="relative">
                <div class="bg-[#f57c00] absolute -right-6 top-1/2 -translate-y-1/2 w-48 h-[80%] rounded-r-3xl z-0"></div>
                <div class="rounded-3xl overflow-hidden shadow-2xl relative z-10 border-8 border-white">
                    <img src="/images/images/about_section_img.webp" alt="Fiesta Catering" class="w-full h-auto">
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Full-width Hero Catchphrase -->
    <section class="h-[500px] relative overflow-hidden flex items-center justify-center text-center px-4">
        <img src="/images/images/about_hero_full.webp" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 max-w-4xl space-y-8">
            <h2 class="text-white text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
                The best food trucks and caterers for your event. Easy planning, diverse enjoyment, unforgettable moments!
            </h2>
            <button class="bg-[#f57c00] p-6 rounded-full hover:scale-110 transition-transform shadow-2xl">
                <svg class="h-10 w-10 text-white fill-current" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" /></svg>
            </button>
        </div>
    </section>

    <!-- 4. Food Concepts grid (Recycled from Home) -->
    <section class="py-24 bg-gray-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-5xl font-snugle text-[#1a130c] mb-12 italic italic">Our Food Concepts at a Glance</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach([
                    ['name' => 'Pasta', 'img' => 'about_concept1.webp'],
                    ['name' => 'Burger', 'img' => 'about_concept2.webp'],
                    ['name' => 'Sweets & Candy', 'img' => 'about_concept3.webp'],
                    ['name' => 'Finger food & snacks', 'img' => 'about_concept4.webp']
                ] as $item)
                <div class="relative rounded-3xl overflow-hidden h-48 group cursor-pointer shadow-lg">
                    <img src="/images/images/{{ $item['img'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-white font-snugle text-3xl italic">{{ $item['name'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-12">
                <a href="#" class="font-snugle text-[#f57c00] underline text-xl hover:scale-110 transition-all inline-block italic">Show Me More</a>
            </div>
        </div>
    </section>
</div>
