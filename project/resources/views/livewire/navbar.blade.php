<header x-data="{ expanded: false }" class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <!-- Brand / Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="group">
                    <img class="h-10 w-auto sm:h-12 group-hover:scale-105 transition-transform" 
                         src="{{ asset('images/logo.webp') }}" 
                         alt="Fiesta Catering">
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-10">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'after:w-full text-fiesta-orange' : '' }}">Home</a>
                <a href="{{ route('become-partner') }}" class="nav-link {{ request()->routeIs('become-partner') ? 'after:w-full text-fiesta-orange' : '' }}">Become A Partner</a>
                <a href="{{ route('caterers') }}" class="nav-link {{ request()->routeIs('caterers') ? 'after:w-full text-fiesta-orange' : '' }}">Caterer</a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'after:w-full text-fiesta-orange' : '' }}">About Us</a>
                <a href="{{ route('faq') }}" class="nav-link {{ request()->routeIs('faq') ? 'after:w-full text-fiesta-orange' : '' }}">FAQ's</a>
                
                <!-- Language Selector -->
                <div class="flex items-center space-x-1 cursor-pointer nav-link">
                    <img src="https://flagcdn.com/w20/us.png" alt="US Flag" class="w-5 h-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden lg:flex items-center space-x-4">
                <a href="#" class="px-6 py-2 rounded-full border-2 border-fiesta-blue text-fiesta-blue font-bold hover:bg-fiesta-blue hover:text-white transition-all text-[14px]">
                    Reach us
                </a>
                <a href="{{ route('login') }}" class="px-8 py-2.5 rounded-full bg-fiesta-dark text-white font-bold hover:bg-fiesta-orange transition-all shadow-md text-[14px]">
                    Login
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 -my-2 lg:hidden">
                <button @click="expanded = !expanded" type="button" class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <span class="sr-only">Open menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!expanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="expanded" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="expanded" x-transition class="lg:hidden bg-white border-t border-gray-100 shadow-xl">
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block py-3 text-lg font-bold text-gray-700 border-b border-gray-50">Home</a>
            <a href="{{ route('become-partner') }}" class="block py-3 text-lg font-bold text-gray-700 border-b border-gray-50">Become A Partner</a>
            <a href="{{ route('caterers') }}" class="block py-3 text-lg font-bold text-gray-700 border-b border-gray-50">Caterer</a>
            <a href="{{ route('about') }}" class="block py-3 text-lg font-bold text-gray-700 border-b border-gray-50">About Us</a>
            <a href="{{ route('faq') }}" class="block py-3 text-lg font-bold text-gray-700 border-b border-gray-50">FAQ's</a>
            <div class="pt-6 flex flex-col space-y-3">
                <a href="#" class="w-full text-center py-3 border-2 border-fiesta-blue rounded-full text-fiesta-blue font-bold">Reach us</a>
                <a href="{{ route('login') }}" class="w-full text-center py-3 bg-fiesta-dark text-white rounded-full font-bold shadow-lg">Login</a>
            </div>
        </div>
    </div>
</header>
