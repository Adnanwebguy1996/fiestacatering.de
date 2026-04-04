<div class="bg-gray-50 min-h-screen">
    <!-- 1. Banner Header -->
    <section class="h-64 relative overflow-hidden flex items-center justify-center p-0">
        <div class="absolute inset-0 flex">
            @foreach(['hero1.webp', 'hero2.webp', 'hero3.webp', 'hero4.webp'] as $img)
            <div class="flex-1">
                <img src="/images/{{ $img }}" class="w-full h-full object-cover">
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
                    <div class="w-full md:w-64">
                        <select wire:model.live="type" class="w-full p-4 rounded-xl border-none focus:ring-2 focus:ring-[#f57c00] text-gray-800 text-lg shadow-sm bg-white cursor-pointer appearance-none">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->truck_category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="md:col-span-4 space-y-2">
                    <label class="text-sm font-semibold text-gray-500 uppercase">Search</label>
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name, address, or zip code..." class="w-full pl-12 pr-4 py-4 rounded-xl border-none focus:ring-2 focus:ring-[#f57c00] text-gray-800 text-lg shadow-sm placeholder-gray-400">
                        <div class="absolute left-4 top-4 text-[#f57c00]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($trucks as $truck)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:-translate-y-2 transition-all duration-300 border border-gray-100 flex flex-col h-full group">
                <div class="relative h-56 overflow-hidden">
                    <div class="absolute inset-0 bg-gray-900/10 group-hover:bg-transparent transition-all z-10"></div>
                    <img src="https://images.unsplash.com/photo-1565557613262-1811e58288cd?auto=format&fit=crop&q=80" alt="{{ $truck->truck_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur pb-1 px-3 py-1 rounded-full text-[#1a130c] font-bold text-sm z-20 flex items-center gap-1 shadow-sm">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        N/A <!-- Star Rating from DB map placeholder -->
                    </div>
                </div>
                
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-2xl font-bold text-[#1a130c] font-snugle">{{ $truck->truck_name }}</h3>
                        <span class="px-3 py-1 bg-fiesta-blue/10 text-[#164fa1] text-xs font-bold rounded-full whitespace-nowrap">{{ $truck->truck_category ?? 'Partner' }}</span>
                    </div>
                    
                    <p class="text-gray-500 flex items-center gap-2 mb-4 text-sm font-medium">
                        <svg class="w-4 h-4 text-[#f57c00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $truck->address ?? 'Location not specified' }}
                    </p>
                    
                    <p class="text-gray-600 line-clamp-3 mb-6 flex-grow">{{ $truck->description ?? 'No description available.' }}</p>
                    
                    <a href="{{ route('caterer.details', $truck->id) }}" class="block w-full py-3 bg-[#164fa1] text-white text-center rounded-xl font-bold hover:bg-[#1a130c] transition-colors mt-auto">See Details</a>
                </div>
            </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-2xl shadow">
                    <p class="text-gray-500 text-lg font-bold">No catering partners found matching your search criteria.</p>
                </div>
            @endforelse
        </div>
        
        <div class="pt-12 flex justify-center">
            @if(method_exists($trucks, 'links'))
                {{ $trucks->links() }}
            @endif
        </div>
    </section>
</div>
