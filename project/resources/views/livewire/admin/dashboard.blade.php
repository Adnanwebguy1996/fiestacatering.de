<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-extrabold text-[#1a130c] font-snugle">Super Admin Panel</h1>
    </div>

    <!-- Analytics Dashboard identical to previous React implementation -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card bg-white p-6 rounded-2xl shadow border border-white/20">
            <h3 class="text-gray-500 font-bold mb-2">Total Users</h3>
            <p class="text-4xl font-extrabold text-[#164fa1]">{{ $total_users }}</p>
        </div>
        <div class="glass-card bg-white p-6 rounded-2xl shadow border border-white/20">
            <h3 class="text-gray-500 font-bold mb-2">Total Partners/Companies</h3>
            <p class="text-4xl font-extrabold text-[#f57c00]">{{ $total_company }}</p>
        </div>
        <div class="glass-card bg-white p-6 rounded-2xl shadow border border-white/20">
            <h3 class="text-gray-500 font-bold mb-2">Total Revenue</h3>
            <p class="text-4xl font-extrabold text-green-600">€{{ number_format($total_revenue, 2) }}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Users Table -->
        <div class="glass-card bg-white rounded-2xl shadow p-6 border border-white/20">
            <h2 class="text-2xl font-bold mb-4 font-snugle text-[#1a130c]">Latest Users</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-2 px-4 font-bold text-gray-700">Name</th>
                            <th class="py-2 px-4 font-bold text-gray-700">Email</th>
                            <th class="py-2 px-4 font-bold text-gray-700 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usersList as $user)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4 text-[#1a130c] font-bold">{{ $user->full_name }}</td>
                            <td class="py-3 px-4 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($user->status == 1)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-gray-400">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Partners Table -->
        <div class="glass-card bg-white rounded-2xl shadow p-6 border border-white/20">
            <h2 class="text-2xl font-bold mb-4 font-snugle text-[#1a130c]">Latest Partners</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-2 px-4 font-bold text-gray-700">Company Name</th>
                            <th class="py-2 px-4 font-bold text-gray-700">Contact</th>
                            <th class="py-2 px-4 font-bold text-gray-700 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partnersList as $partner)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <p class="text-[#f57c00] font-bold">{{ $partner->company_name ?? 'Pending Review' }}</p>
                                <p class="text-xs text-gray-500">{{ $partner->city_name }}</p>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-500">
                                {{ $partner->full_name }} <br> <span class="text-xs">{{ $partner->email }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($partner->status == 1)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-gray-400">No partners found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
