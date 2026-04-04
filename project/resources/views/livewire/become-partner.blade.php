<div class="bg-white min-h-screen">
    <!-- 1. Hero Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 flex flex-col lg:flex-row items-center gap-12">
        <div class="lg:w-1/2 space-y-8">
            <h1 class="text-5xl md:text-6xl font-bold text-[#164fa1] leading-tight">
                Fewer cancellations.<br>
                More bookings.<br>
                100% commission-free.
            </h1>
            <a href="{{ route('register') }}" class="inline-block btn-orange px-10 py-4 text-xl font-bold rounded-full shadow-lg hover:shadow-[#f57c00]/40">
                Start for free now
            </a>
        </div>
        <div class="lg:w-1/2 relative">
            <img src="/images/Partner.webp" alt="Partner Hero" class="w-full h-auto rounded-3xl drop-shadow-2xl">
            <!-- Floating Bubble -->
            <div class="absolute -top-10 right-0 bg-white p-6 rounded-full shadow-xl border border-gray-100 max-w-[180px] text-center hidden md:block">
                <p class="text-sm font-bold text-[#1a130c]">Do you have questions? Then click You here.</p>
            </div>
        </div>
    </section>

    <!-- 2. Why FiestaCatering? -->
    <section class="max-w-6xl mx-auto px-4 py-20">
        <div class="bg-[#f9f9f9] rounded-[40px] p-12 text-center shadow-sm border border-gray-100">
            <h2 class="text-4xl font-bold text-[#164fa1] mb-2">Why FiestaCatering?</h2>
            <p class="text-gray-500 mb-12">The platform for food trucks & caterers – simple, visible, successful.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 text-left max-w-4xl mx-auto">
                @foreach([
                    'No commission – You keep 100% of your revenue',
                    'Targeted inquiries – Directly from real event organizers',
                    'Social media push – We present you on our channels'
                ] as $feature)
                <div class="flex items-center space-x-3">
                    <div class="bg-[#164fa1] p-1.5 rounded-full">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-gray-700 font-semibold">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 3. Membership Choice -->
    <section class="max-w-7xl mx-auto px-4 py-20 text-center">
        <h2 class="text-4xl font-bold text-[#1a130c] mb-12 flex items-center justify-center gap-2">
            <span class="text-[#f57c00] text-2xl">*</span> 1. Membership choice
        </h2>
        <p class="text-[#f57c00] mb-8 font-medium italic">Please select the membership which is given below</p>

        <!-- Toggle (Animated mockup) -->
        <div class="flex items-center justify-center mb-20 relative">
            <div class="bg-white border-2 border-gray-100 rounded-full p-1 flex shadow-inner">
                <button class="bg-[#f57c00] text-white px-8 py-2 rounded-full font-bold shadow-md">Monthly</button>
                <button class="text-gray-400 px-8 py-2 rounded-full font-bold hover:text-gray-600 transition-colors">Yearly</button>
            </div>
            <!-- Arrow -->
            <div class="absolute left-[calc(50%+110px)] top-0 hidden lg:block">
                <p class="text-[#164fa1] font-bold text-sm mb-1 italic">Save up to 10%</p>
                <svg class="w-16 h-8 text-[#164fa1]" fill="none" stroke="currentColor" viewBox="0 0 100 20" stroke-width="2" stroke-linecap="round"><path d="M5 5 Q 50 25 95 5 M 95 5 L 85 5 M 95 5 L 95 15" /></svg>
            </div>
        </div>

        <!-- Pricing Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Free Trial -->
            <div class="bg-[#e2f3ff] rounded-[30px] p-8 flex flex-col hover-scale border-2 border-transparent">
                <div class="bg-white/50 text-[#164fa1] font-bold text-sm px-4 py-1.5 rounded-full self-center mb-6">Free - Trial</div>
                <div class="text-[#164fa1] text-3xl font-bold mb-8">0€ <span class="text-lg font-normal">/ Monat</span></div>
                <img src="/images/food-truck 2.webp" class="h-20 w-auto mx-auto mb-8 opacity-40 grayscale">
                <p class="text-sm text-gray-600 mb-8 italic leading-relaxed">Try our platform free for 30 days - no commitment!</p>
                <ul class="text-left space-y-3 mb-10 flex-grow text-[13px] text-gray-700">
                    <li>• Full access to all Basic plan features</li>
                    <li>• Receive and test your first customer inquiries</li>
                    <li>• No commission on orders – even during the trial!</li>
                    <li>• No automatic renewal – you decide what happens next</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 bg-[#00befa] text-white text-center rounded-xl font-bold shadow-lg hover:bg-[#164fa1]">Select</a>
            </div>

            <!-- Basic -->
            <div class="bg-[#fff4eb] rounded-[30px] p-8 flex flex-col hover-scale border-2 border-transparent">
                <div class="bg-white/50 text-[#f57c00] font-bold text-sm px-4 py-1.5 rounded-full self-center mb-6">Basic</div>
                <div class="text-[#f57c00] text-3xl font-bold mb-8">16.99€ <span class="text-lg font-normal">/ Monat</span></div>
                <img src="/images/food-truck 2.webp" class="h-20 w-auto mx-auto mb-8 opacity-60">
                <p class="text-sm text-gray-600 mb-8 italic leading-relaxed">Get started professionally and showcase your food truck or catering service.</p>
                <ul class="text-left space-y-3 mb-10 flex-grow text-[13px] text-gray-700">
                    <li>• Manage one profile – simple and efficient</li>
                    <li>• Be visible to potential customers on our platform</li>
                    <li>• No commission on orders - just the subscription fee</li>
                    <li>• The subscription can be cancelled at the end of each term. If no cancellation is received in due time, the subscription will automatically renew for the same term at the price of €16.99.</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 bg-[#f57c00] text-white text-center rounded-xl font-bold shadow-lg hover:bg-[#e67300]">Select</a>
            </div>

            <!-- Premium -->
            <div class="bg-[#e8effd] rounded-[30px] p-8 flex flex-col hover-scale border-2 border-[#164fa1] relative shadow-2xl">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-[#164fa1] text-white px-6 py-1 rounded-full text-xs font-bold uppercase tracking-widest">Best Choice</div>
                <div class="bg-white/50 text-[#164fa1] font-bold text-sm px-4 py-1.5 rounded-full self-center mb-6">Premium</div>
                <div class="text-[#164fa1] text-3xl font-bold mb-8">24.99€ <span class="text-lg font-normal">/ Monat</span></div>
                <img src="/images/food-truck 2.webp" class="h-20 w-auto mx-auto mb-8">
                <p class="text-sm text-gray-600 mb-8 italic leading-relaxed">More reach, more inquiries, more opportunities!</p>
                <ul class="text-left space-y-3 mb-10 flex-grow text-[13px] text-gray-700">
                    <li>• Manage up to three food truck or catering profiles</li>
                    <li>• Exclusive access to the FiestaCatering marketplace – all inquiries sent directly to you</li>
                    <li>• Trust badge on your profile – the star boosts visibility & bookings</li>
                    <li>• Social media power – We regularly post about you on our channels</li>
                    <li>• No commission – you keep 100% of your revenue</li>
                    <li>• The subscription can be cancelled at the end of each term. If no cancellation is received in due time, the subscription will automatically renew for the same term at the price of €24.99.</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 bg-[#164fa1] text-white text-center rounded-xl font-bold shadow-lg hover:bg-[#1a130c]">Select</a>
            </div>
        </div>
    </section>
</div>
