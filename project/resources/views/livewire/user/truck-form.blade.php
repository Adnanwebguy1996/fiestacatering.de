<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('user.dashboard') }}" class="text-[#164fa1] hover:underline flex items-center gap-2 mb-4">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-extrabold text-[#1a130c] font-snugle">Add a New Food Truck</h1>
        <p class="text-gray-500 mt-2">Fill out the details below to list your food truck or catering service.</p>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 glass-card">
        <form wire:submit.prevent="save" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Truck Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700">Truck / Service Name</label>
                    <input wire:model.defer="truck_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f57c00] focus:ring-[#f57c00] p-2 border" required>
                    @error('truck_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-bold text-gray-700">Category</label>
                    <select wire:model.defer="truck_cat_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f57c00] focus:ring-[#f57c00] p-2 border bg-white" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->truck_category }}</option>
                        @endforeach
                        <!-- Fallback if empty db -->
                        @if(empty($categories))
                            <option value="1">Street Food</option>
                            <option value="2">Food Truck</option>
                            <option value="3">Catering</option>
                        @endif
                    </select>
                    @error('truck_cat_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700">Base Location / Address</label>
                    <input wire:model.defer="address" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f57c00] focus:ring-[#f57c00] p-2 border" required>
                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Size -->
                <div>
                    <label class="block text-sm font-bold text-gray-700">Size (e.g. 5x3m, Small, Large)</label>
                    <input wire:model.defer="size" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f57c00] focus:ring-[#f57c00] p-2 border" required>
                    @error('size') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Operating Mode -->
                <div>
                    <label class="block text-sm font-bold text-gray-700">Operating Mode</label>
                    <select wire:model.defer="operating_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f57c00] focus:ring-[#f57c00] p-2 border bg-white" required>
                        <option value="">Select mode</option>
                        <option value="Event">Event Based</option>
                        <option value="Daily">Daily Fixed Location</option>
                        <option value="Both">Both</option>
                    </select>
                    @error('operating_mode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700">Description</label>
                    <textarea wire:model.defer="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#f57c00] focus:ring-[#f57c00] p-2 border"></textarea>
                </div>

                <!-- Water Required -->
                <div class="md:col-span-2 flex items-center">
                    <input wire:model.defer="is_water_required" type="checkbox" value="1" class="h-4 w-4 text-[#f57c00] focus:ring-[#f57c00] border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Requires dedicated water connection?</label>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="btn-blue px-10 py-3 rounded-xl font-bold shadow-lg hover:shadow-[#164fa1]/40 flex items-center justify-center">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Save Vehicle / Service
                </button>
            </div>
        </form>
    </div>
</div>
