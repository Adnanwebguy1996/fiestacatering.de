<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6 flex space-x-2 text-sm text-gray-500 font-bold">
            <a href="{{ route('caterers') }}" class="hover:text-[#f57c00]">All Caterers</a>
            <span>/</span>
            <span class="text-[#1a130c]">{{ $truck->truck_name }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Left Profile Section -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-3xl shadow p-8 border border-gray-100">
                    <h1 class="text-4xl font-extrabold text-[#1a130c] font-snugle mb-4">{{ $truck->truck_name }}</h1>
                    <div class="flex gap-4 items-center mb-6 border-b border-gray-100 pb-6">
                        <span class="px-4 py-2 bg-fiesta-blue/10 text-[#164fa1] font-bold rounded-full">{{ $truck->truck_category ?? 'Partner' }}</span>
                        <div class="flex items-center text-yellow-500 font-bold">
                            <!-- Star icon -->
                            ★ 4.9 (12 reviews)
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-3">About this service</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        {{ $truck->description ?? 'This caterer has not provided an extended description. Please fill out the booking form below to send an inquiry directly.' }}
                    </p>

                    <h3 class="text-xl font-bold mb-3">Location Details</h3>
                    <p class="text-gray-600 mb-2"><strong>Base Address:</strong> {{ $truck->address }}</p>
                    <p class="text-gray-600 mb-2"><strong>Operating Model:</strong> {{ $truck->operating_mode }}</p>
                    <p class="text-gray-600 mb-2"><strong>Truck Size:</strong> {{ $truck->size }}</p>
                </div>
            </div>

            <!-- Right Booking Box -->
            <div>
                <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100 sticky top-10">
                    <h2 class="text-2xl font-bold text-[#1a130c] mb-6">Request a Booking</h2>
                    
                    @if (session()->has('message'))
                        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                            {{ session('message') }}
                        </div>
                    @endif
                    
                    @if (session()->has('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="book" class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Full Name</label>
                            <input wire:model.defer="full_name" type="text" class="mt-1 w-full border-gray-300 rounded-xl bg-gray-50 focus:ring-[#f57c00]" required>
                            @error('full_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Phone</label>
                                <input wire:model.defer="phno" type="text" class="mt-1 w-full border-gray-300 rounded-xl bg-gray-50 focus:ring-[#f57c00]" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Email</label>
                                <input wire:model.defer="email" type="email" class="mt-1 w-full border-gray-300 rounded-xl bg-gray-50 focus:ring-[#f57c00]" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700">Event Address / City</label>
                            <div class="grid grid-cols-2 gap-4">
                                <input wire:model.defer="address" type="text" placeholder="Street" class="mt-1 w-full border-gray-300 rounded-xl bg-gray-50 focus:ring-[#f57c00]" required>
                                <input wire:model.defer="city_name" type="text" placeholder="City" class="mt-1 w-full border-gray-300 rounded-xl bg-gray-50 focus:ring-[#f57c00]" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">From Date</label>
                                <input wire:model.defer="from_date" type="date" class="mt-1 w-full border-gray-300 rounded-xl bg-gray-50 focus:ring-[#f57c00]" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">To Date</label>
                                <input wire:model.defer="to_date" type="date" class="mt-1 w-full border-gray-300 rounded-xl bg-gray-50 focus:ring-[#f57c00]" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Guests</label>
                                <input wire:model.live="no_of_person" type="number" min="1" class="mt-1 w-full border-gray-300 rounded-xl focus:ring-[#f57c00]">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Budget / person (€)</label>
                                <input wire:model.live="budget_per_person" type="number" min="1" class="mt-1 w-full border-gray-300 rounded-xl focus:ring-[#f57c00]">
                            </div>
                        </div>
                        
                        <div class="pt-2 pb-4 text-right">
                            <span class="text-sm text-gray-500">Estimated Budget:</span>
                            <span class="text-2xl font-bold text-[#f57c00]">€{{ number_format($total_budget, 2) }}</span>
                        </div>

                        <button type="submit" class="w-full btn-blue py-3 rounded-xl font-bold flex justify-center items-center">
                            <span wire:loading.remove wire:target="book">Send Booking Inquiry</span>
                            <span wire:loading wire:target="book">Processing...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
