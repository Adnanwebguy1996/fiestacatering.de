<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Create an account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Already have an account?
            <a href="/login" class="font-medium text-[#f57c00] hover:text-[#e67300]">
                Sign in
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form wire:submit.prevent="register" class="space-y-6">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">
                        Full Name
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="full_name" id="full_name" name="full_name" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#f57c00] focus:border-[#f57c00] sm:text-sm">
                    </div>
                    @error('full_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="email" id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#f57c00] focus:border-[#f57c00] sm:text-sm">
                    </div>
                    @error('email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="country_id" class="block text-sm font-medium text-gray-700">
                        Country
                    </label>
                    <div class="mt-1">
                        <select wire:model.defer="country_id" id="country_id" name="country_id" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#f57c00] focus:border-[#f57c00] sm:text-sm bg-white">
                            <option value="">Select a country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name ?? 'Unknown' }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('country_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="password" id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#f57c00] focus:border-[#f57c00] sm:text-sm">
                    </div>
                    @error('password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#f57c00] focus:border-[#f57c00] sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-fiesta-dark hover:bg-fiesta-orange focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f57c00] transition-colors">
                        Sign up
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
