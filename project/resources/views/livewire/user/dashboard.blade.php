<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-[#1a130c] font-snugle">Partner Dashboard</h1>
        <a href="{{ route('user.trucks.create') }}" class="btn-orange px-6 py-2 rounded-xl font-bold shadow-lg hover:shadow-[#f57c00]/40 transition-shadow">
            + Add Food Truck
        </a>
    </div>

    @if(session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <p class="text-sm text-green-700">{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg bg-opacity-70 p-6 glass-card border border-white/20">
        <h2 class="text-xl font-bold mb-4">My Food Trucks & Caterings</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($trucks as $truck)
                <div class="border rounded-lg p-4 flex flex-col justify-between shadow-sm bg-gray-50 hover:shadow-md transition-shadow">
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="text-xl font-bold text-[#1a130c]">{{ $truck->truck_name }}</h3>
                            <span class="px-3 py-1 bg-gray-200 text-xs rounded-full">{{ $truck->truck_category ?? 'General' }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $truck->description }}</p>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <a href="#" class="px-4 py-2 border border-[#f57c00] text-[#f57c00] rounded-lg font-bold hover:bg-[#f57c00] hover:text-white transition-colors text-sm">Edit</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <p class="text-gray-500 mb-4">You haven't listed any food trucks yet.</p>
                    <a href="{{ route('user.trucks.create') }}" class="text-[#f57c00] font-bold hover:underline">Get started by adding your first one!</a>
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white shadow rounded-lg bg-opacity-70 p-6 glass-card border border-white/20 mt-8">
        <h2 class="text-xl font-bold mb-4 font-snugle text-[#1a130c]">Incoming Inquiries & Bookings</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-700">Client / Contact</th>
                        <th class="py-3 px-4 font-bold text-gray-700">Event Dates</th>
                        <th class="py-3 px-4 font-bold text-gray-700">Total Budget</th>
                        <th class="py-3 px-4 font-bold text-gray-700">Guests</th>
                        <th class="py-3 px-4 font-bold text-gray-700 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4">
                                <p class="font-bold text-[#1a130c]">{{ $booking->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->email }} • {{ $booking->phno }}</p>
                            </td>
                            <td class="py-4 px-4 text-sm">
                                {{ \Carbon\Carbon::parse($booking->from_date)->format('M d, Y') }} <br>
                                to {{ \Carbon\Carbon::parse($booking->to_date)->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-4 font-bold text-[#f57c00]">
                                €{{ number_format($booking->total_budget, 2) }}
                            </td>
                            <td class="py-4 px-4 text-gray-700">
                                {{ $booking->no_of_person }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($booking->booking_status == 0)
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">Pending</span>
                                @elseif($booking->booking_status == 1)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Approved</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                You have no booking requests at this time.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
