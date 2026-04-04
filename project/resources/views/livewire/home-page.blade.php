<div class="overflow-hidden">
    <!-- 1. Hero Section (High Fidelity Refined) -->
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
        <!-- Background Banner -->
        <div class="absolute inset-0 flex">
            @foreach(['hero1.webp', 'hero2.webp', 'hero3.webp', 'hero4.webp'] as $image)
            <div class="flex-1 h-full">
                <img src="{{ asset('images/' . $image) }}" class="w-full h-full object-cover" alt="Event Image">
            </div>
            @endforeach
        </div>
        
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px]"></div>

        <!-- Glassmorphism Hero Content -->
        <div class="relative z-10 w-full max-w-4xl px-4">
            <div class="glass-card p-12 md:p-16 text-center space-y-8">
                <div class="flex items-center justify-center space-x-4 mb-2">
                    <div class="h-[1px] w-12 bg-fiesta-orange"></div>
                    <span class="text-fiesta-orange font-bold uppercase tracking-[0.2em] text-sm">Marketplace For Events</span>
                    <div class="h-[1px] w-12 bg-fiesta-orange"></div>
                </div>

                <h1 class="text-6xl md:text-8xl font-snugle text-white leading-tight drop-shadow-lg">
                    Fiesta Catering
                </h1>

                <p class="text-xl md:text-2xl text-white/90 italic font-medium max-w-2xl mx-auto leading-relaxed">
                    Are you planning an event or looking for your next food truck?
                </p>

                <div class="pt-4 space-y-4">
                    <p class="text-white/80 text-sm md:text-base font-medium tracking-wide">
                        Discover the right catering brand.<br>
                        Find food of your choice or become our platform as a partner.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mt-8">
                        <a href="{{ route('caterers') }}" class="btn-orange w-full sm:w-auto">
                            Find a food truck
                        </a>
                        <a href="{{ route('become-partner') }}" class="btn-blue w-full sm:w-auto">
                            Become partner
                        </a>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('caterers') }}" class="nav-link text-fiesta-orange text-lg">
                            Search catering?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Search Section (High Fidelity) -->
    <section class="py-12 bg-gray-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl p-8 -mt-24 relative z-20 border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-11 gap-6 items-end">
                    <!-- Food Truck Category -->
                    <div class="md:col-span-4 space-y-2">
                        <label class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Food Truck Category</label>
                        <div class="relative">
                            <select class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-700 appearance-none focus:ring-2 focus:ring-[#f57c00] focus:border-transparent outline-none">
                                <option>Select Category</option>
                                <option>Burger</option>
                                <option>Pizza</option>
                                <option>Tacos</option>
                            </select>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- City/Location -->
                    <div class="md:col-span-4 space-y-2">
                        <label class="text-sm font-semibold text-gray-500 uppercase tracking-wider">City/Location</label>
                        <div class="relative">
                            <input type="text" placeholder="Select Location" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-700 focus:ring-2 focus:ring-[#f57c00] focus:border-transparent outline-none">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <svg class="h-5 w-5 text-[#f57c00]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Create Button -->
                    <div class="md:col-span-3">
                        <button class="w-full btn-blue py-4 font-bold text-lg rounded-xl shadow-lg hover:shadow-[#164fa1]/40">
                            Create
                        </button>
                    </div>
                </div>

                <!-- Filters Rows -->
                <div class="mt-6 flex flex-wrap gap-8 items-center border-t border-gray-100 pt-6">
                    <label class="inline-flex items-center space-x-3 cursor-pointer group">
                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-[#f57c00] focus:ring-[#f57c00] cursor-pointer">
                        <span class="text-gray-600 font-medium group-hover:text-[#f57c00]">Caterer</span>
                    </label>
                    <label class="inline-flex items-center space-x-3 cursor-pointer group">
                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-[#f57c00] focus:ring-[#f57c00] cursor-pointer">
                        <span class="text-gray-600 font-medium group-hover:text-[#f57c00]">Events</span>
                    </label>
                    <label class="inline-flex items-center space-x-3 cursor-pointer group">
                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-[#f57c00] focus:ring-[#f57c00] cursor-pointer" checked>
                        <span class="text-gray-600 font-medium group-hover:text-[#f57c00]">Both</span>
                    </label>
                    <label class="inline-flex items-center space-x-3 cursor-pointer group">
                        <input type="checkbox" class="w-5 h-5 rounded border-gray-300 text-[#f57c00] focus:ring-[#f57c00] cursor-pointer">
                        <span class="text-gray-600 font-medium group-hover:text-[#f57c00]">Show on Map</span>
                    </label>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Discover Section (High Fidelity) -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-5xl font-snugle text-[#1a130c] mb-2 italic">Discover Caterer</h2>
            <p class="text-gray-500 mb-12 max-w-2xl mx-auto">Discover the perfect food truck and professional caterers for your event with just a few clicks.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Example Card 1 -->
                <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 hover-scale group cursor-pointer">
                    <div class="h-64 bg-gray-200 relative overflow-hidden">
                        <img src="/images/catering1.webp" alt="Street Food Fiesta" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 right-4 bg-orange-500 text-white p-2 rounded-full shadow-lg">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        </div>
                    </div>
                    <div class="p-6 text-left">
                        <h3 class="text-2xl font-snugle text-[#1a130c] mb-1">Street Food Fiesta</h3>
                        <p class="text-xs font-bold text-[#f57c00] uppercase tracking-widest mb-4">Caterer</p>
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2 italic">A authentic taste of Spanish food culture with a variety of street food and traditional dishes.</p>
                        <a href="{{ route('caterers') }}" class="block w-full py-3 bg-[#164fa1] text-white text-center rounded-xl font-bold hover:bg-[#1a130c] transition-colors">See Details</a>
                    </div>
                </div>

                <!-- Example Card 2 -->
                <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 hover-scale group cursor-pointer">
                    <div class="h-64 bg-gray-200 relative overflow-hidden">
                        <img src="/images/catering2.webp" alt="Vicenza" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="p-6 text-left">
                        <h3 class="text-2xl font-snugle text-[#1a130c] mb-1">Vicenza</h3>
                        <p class="text-xs font-bold text-[#f57c00] uppercase tracking-widest mb-4">Food Truck</p>
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2 italic">Fresh pasta, pizza, and Italian specialties served from our mobile kitchen for your event.</p>
                        <a href="{{ route('caterers') }}" class="block w-full py-3 bg-[#164fa1] text-white text-center rounded-xl font-bold hover:bg-[#1a130c] transition-colors">See Details</a>
                    </div>
                </div>

                <!-- Example Card 3 -->
                <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 hover-scale group cursor-pointer">
                    <div class="h-64 bg-gray-200 relative overflow-hidden">
                        <img src="/images/catering3.webp" alt="Sunny Food Truck" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute top-4 right-4 bg-orange-500 text-white p-2 rounded-full shadow-lg">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        </div>
                    </div>
                    <div class="p-6 text-left">
                        <h3 class="text-2xl font-snugle text-[#1a130c] mb-1">Sunny Food Truck</h3>
                        <p class="text-xs font-bold text-[#f57c00] uppercase tracking-widest mb-4">Food Truck</p>
                        <p class="text-sm text-gray-500 mb-6 line-clamp-2 italic">Healthy, fresh, and local ingredients prepared on site for a unique culinary experience.</p>
                        <a href="{{ route('caterers') }}" class="block w-full py-3 bg-[#164fa1] text-white text-center rounded-xl font-bold hover:bg-[#1a130c] transition-colors">See Details</a>
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <a href="{{ route('caterers') }}" class="font-snugle text-[#f57c00] underline text-xl hover:scale-110 transition-all inline-block">
                    Show Me More
                </a>
            </div>
        </div>
    </section>

    <!-- 4. Fiesta Catering Concepts -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-5xl font-snugle text-[#1a130c] mb-12 italic">Fiesta Catering Concepts</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-3xl overflow-hidden h-64 hover-scale cursor-pointer">
                    <img src="concept1.webp" class="w-full h-full object-cover">
                </div>
                <div class="h-64 rounded-3xl overflow-hidden shadow-2xl relative">
                    <img src="concept2.webp" class="w-full h-full object-cover">
                </div>
                <div class="h-64 rounded-3xl overflow-hidden shadow-2xl relative md:col-span-2">
                    <img src="concept3.webp" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Our Top Caterers -->
    <section class="py-20 bg-white relative overflow-hidden">
        <!-- Subtle background glow -->
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-orange-50/50 to-transparent pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-5xl font-snugle text-[#1a130c] mb-2 italic">Our Top Caterers</h2>
            <p class="text-gray-500 mb-12 max-w-2xl mx-auto italic">Results of our caterers! These caterers have been highly rated by our customers for their outstanding service and quality delivery.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Top Caterer 1 -->
                <div class="bg-white rounded-3xl overflow-hidden shadow-lg border-t-4 border-[#164fa1] hover-scale group">
                    <div class="h-48 bg-gray-100 flex items-center justify-center p-8">
                        <img src="top_caterer1.webp" class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform">
                    </div>
                    <div class="p-6 text-left">
                        <h3 class="text-xl font-bold text-[#1a130c]">Wunderdar</h3>
                        <p class="text-xs text-[#f57c00] font-bold uppercase mb-4">Caterer</p>
                        <a href="{{ route('caterers') }}" class="block w-full py-3 bg-[#164fa1] text-white text-center rounded-xl font-bold hover:bg-[#f57c00]">View Profile</a>
                    </div>
                </div>
                <!-- Top Caterer 2 -->
                <div class="glass-card bg-white p-8 rounded-3xl shadow flex flex-col items-center text-center group">
                    <div class="h-32 mb-6 flex items-center justify-center">
                        <img src="top_caterer2.webp" class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform">
                    </div>
                </div>
                <!-- Box 3 -->
                <div class="glass-card bg-white p-8 rounded-3xl shadow flex flex-col items-center text-center group">
                    <div class="h-32 mb-6 flex items-center justify-center">
                        <img src="top_caterer3.webp" class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform">
                    </div>
                    <div class="p-6 text-left">
                        <h3 class="text-xl font-bold text-[#1a130c]">Sunny Food Truck</h3>
                        <p class="text-xs text-[#f57c00] font-bold uppercase mb-4">Food Truck</p>
                        <a href="{{ route('caterers') }}" class="block w-full py-3 bg-[#164fa1] text-white text-center rounded-xl font-bold hover:bg-[#f57c00]">View Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Our Food Concepts at a Glance -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-5xl font-snugle text-[#1a130c] mb-12 italic">Our Food Concepts at a Glance</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach(['Pasta', 'Burger', 'Sweets & Candy', 'Finger food & snacks'] as $concept)
                <button class="bg-gray-500 text-white font-snugle text-2xl py-8 rounded-xl shadow-lg hover:bg-[#164fa1] transition-all transform hover:-translate-y-1">
                    {{ $concept }}
                </button>
                @endforeach
            </div>
            <div class="mt-12">
                <a href="{{ route('caterers') }}" class="font-snugle text-[#f57c00] underline text-lg hover:scale-110 transition-all inline-block">Show Me More</a>
            </div>
        </div>
    </section>

    <!-- 7. About Us Section -->
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl relative z-10">
                        <img src="about_main.webp" class="w-full h-auto">
                    </div>
                    <div class="col-span-5 rounded-3xl overflow-hidden shadow-xl mt-12 md:-ml-8 md:mt-24 z-10 border-4 border-white">
                        <img src="about_sub.webp" class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="space-y-8">
                    <h2 class="text-5xl font-snugle text-[#1a130c] italic">About Us</h2>
                    <p class="text-gray-600 text-lg leading-relaxed italic">
                        Fiesta Catering is the go-to platform for finding and booking various food trucks and caterers for any occasion. Let the variety surprise you and make every event special and handle events!
                    </p>
                    <ul class="space-y-4">
                        @foreach(['Largest selection of diverse food trucks and caterers.', 'Easy & reliable booking for any event size.', 'Professional advice & support for your individual event needs.'] as $feature)
                        <li class="flex items-start space-x-4">
                            <div class="mt-1 p-1 bg-blue-100 rounded-lg">
                                <svg class="h-5 w-5 text-[#164fa1]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <span class="text-gray-700 font-medium italic">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('about') }}" class="inline-block btn-blue px-10 rounded-xl font-bold py-4">Find me more</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. Event & Catering Section -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-5xl font-snugle text-[#1a130c] mb-2 italic">Event & Catering</h2>
            <p class="text-gray-500 mb-16 italic">Our caterers have a solution for every type of event need!</p>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <!-- Large Image Card -->
                <div class="lg:col-span-5 rounded-3xl overflow-hidden shadow-xl">
                    <img src="/images/event_main.webp" class="w-full h-full object-cover">
                </div>
                <!-- Features Grid -->
                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach([
                        ['title' => 'COMPANY', 'desc' => 'Professional catering for your corporate events and meetings.'],
                        ['title' => 'FESTIVALS', 'desc' => 'Diverse food options for large scale festivals and concerts.'],
                        ['title' => 'WEDDINGS', 'desc' => 'Unforgettable culinary experiences for your special day.'],
                        ['title' => 'PRIVATE PARTIES', 'desc' => 'Tailored food truck service for birthdays and celebrations.'],
                        ['title' => 'SCHOOL EVENTS', 'desc' => 'Healthy and fun food solutions for schools and universities.'],
                        ['title' => 'MARKETS', 'desc' => 'Authentic street food for local markets and fairs.']
                    ] as $item)
                    <div class="bg-white p-6 rounded-3xl shadow-md border border-gray-100 flex flex-col items-center text-center hover-scale">
                        <div class="mb-4 bg-orange-50 p-3 rounded-2xl">
                            <svg class="h-8 w-8 text-[#f57c00]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2z" /></svg>
                        </div>
                        <h4 class="font-snugle text-xl text-[#1a130c] mb-2">{{ $item['title'] }}</h4>
                        <p class="text-sm text-gray-400 italic leading-snug">{{ $item['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-16">
                <a href="{{ route('caterers') }}" class="btn-blue px-12 py-4 rounded-xl font-bold text-lg shadow-xl hover:shadow-[#164fa1]/40 inline-block">Find your caterer</a>
            </div>
        </div>
    </section>

    <!-- 9. Newsletter Section -->
    <section class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-5xl font-snugle text-[#1a130c] mb-4 italic">Subscribe to our Newsletter</h2>
            <p class="text-gray-500 mb-10 italic">Be the first to know about new food trucks, special offers, and event highlights!</p>
            
            <form class="max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl p-2 flex flex-col sm:flex-row gap-2 border border-gray-100">
                <input type="email" placeholder="Enter your email" class="flex-1 px-6 py-4 rounded-xl outline-none text-gray-700 bg-gray-50 focus:bg-white transition-colors">
                <button type="submit" class="btn-blue px-10 py-4 rounded-xl font-bold text-lg">Subscribe</button>
            </form>
        </div>
    </section>
</div>
