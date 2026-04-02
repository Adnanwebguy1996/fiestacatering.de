<div class="bg-gray-50 min-h-screen">
    <!-- 1. Banner Header -->
    <section class="h-64 relative overflow-hidden flex items-center justify-center p-0">
        <div class="absolute inset-0 flex">
            @foreach(['hero1.webp', 'hero2.webp', 'hero3.webp', 'hero4.webp'] as $img)
            <div class="flex-1">
                <img src="/images/home/{{ $img }}" class="w-full h-full object-cover">
            </div>
            @endforeach
        </div>
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-10 text-center text-white">
            <h1 class="text-6xl font-snugle mb-2">Fiesta Catering</h1>
            <div class="flex items-center justify-center space-x-2 text-xl font-snugle text-[#f57c00]">
                <span>Home</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-white">Caterer</span>
            </div>
        </div>
    </section>

    <!-- 2. Filter Bar (Recycled from Home) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20">
        <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-11 gap-6 items-end">
                <div class="md:col-span-4 space-y-2">
                    <label class="text-sm font-semibold text-gray-500 uppercase">Food Truck Category</label>
                    <select class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-[#f57c00]">
                        <option>Select Category</option>
                        <option>Burger</option>
                        <option>Pizza</option>
                    </select>
                </div>
                <div class="md:col-span-4 space-y-2">
                    <label class="text-sm font-semibold text-gray-500 uppercase">City/Location</label>
                    <input type="text" placeholder="Select Location" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-[#f57c00]">
                </div>
                <div class="md:col-span-3">
                    <button class="w-full btn-blue py-4 font-bold text-lg rounded-xl">Create</button>
                </div>
            </div>
            <div class="mt-6 flex flex-wrap gap-8 items-center border-t border-gray-100 pt-6">
                <!-- Similar Checkboxes -->
                <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" checked class="w-5 h-5 text-[#f57c00] rounded"> <span class="text-gray-600 font-medium">Caterer</span></label>
                <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" class="w-5 h-5 text-[#f57c00] rounded"> <span class="text-gray-600 font-medium">Events</span></label>
                <label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" class="w-5 h-5 text-[#f57c00] rounded"> <span class="text-gray-600 font-medium">Show on Map</span></label>
            </div>
        </div>
    </div>

    <!-- 3. Results Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $tags = ['Food Truck', 'Caterer', 'Beverages'];
                $names = ['Street Food Fiesta', 'Vicenza', 'Sunny Food Truck', 'Burger Station', 'Wunderdar', 'La Pizza'];
            @endphp
            @foreach(range(1, 9) as $i)
            <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 hover-scale group cursor-pointer">
                <div class="h-64 bg-gray-200 relative overflow-hidden">
                    <img src="/images/images/catering{{ ($i % 3) + 1 }}.webp" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute top-4 right-4 bg-orange-500 text-white p-2 rounded-full shadow-lg">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                    </div>
                </div>
                <div class="p-6 text-left">
                    <h3 class="text-2xl font-snugle text-[#1a130c] mb-1 italic">{{ $names[$i % 6] }}</h3>
                    <p class="text-xs font-bold text-[#f57c00] uppercase tracking-widest mb-4 italic">{{ $tags[$i % 3] }}</p>
                    <p class="text-sm text-gray-500 mb-6 line-clamp-2 italic italic">Authentic tastes and traditional dishes prepared fresh for your event participants.</p>
                    <div class="flex gap-2 mb-4">
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-bold text-gray-500">Vegan</span>
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-bold text-gray-500">Bio</span>
                    </div>
                    <a href="#" class="block w-full py-3 bg-[#164fa1] text-white text-center rounded-xl font-bold hover:bg-[#1a130c] transition-colors">See Details</a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination Mockup -->
        <div class="mt-16 flex justify-center space-x-2">
            <button class="w-10 h-10 rounded-lg flex items-center justify-center border border-gray-200 text-[#164fa1] hover:bg-[#164fa1] hover:text-white transition-all">1</button>
            <button class="w-10 h-10 rounded-lg flex items-center justify-center border border-gray-200 text-gray-400 hover:border-[#164fa1] hover:text-[#164fa1] transition-all">2</button>
            <button class="w-10 h-10 rounded-lg flex items-center justify-center border border-gray-200 text-gray-400 hover:border-[#164fa1] hover:text-[#164fa1] transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </section>
</div>
