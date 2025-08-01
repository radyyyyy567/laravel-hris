<div class="fixed inset-0 z-[60] overflow-auto">
    <div class="flex min-h-full items-center justify-center bg-gray-50 dark:bg-gray-900">
        {{-- YOUR CUSTOM HEADER AND FORM --}}
        <div class="w-full bg-[#418CED] h-screen flex-col items-center flex justify-end space-y-10">
            <div class="">
                <img src="{{ asset('assets/bg-login.svg') }}" alt="Login Background" class="h-full w-full object-cover">
            </div>
            {{-- Login Form --}}
            <div class="rounded-xl bg-white p-8 shadow dark:bg-gray-800 rounded-b-none w-full flex justify-center">
                <div class="w-[360px]">
                    <div class="mb-8 text-center">
                    <p class="mt-2 text-[24px] font-semibold text-blue-500 dark:text-gray-400">
                        Login
                    </p>
                </div>
                
                <form wire:submit="authenticate" class="space-y-6">
                    @csrf
                    
                    {{-- Email Input --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="data.email"
                            wire:model="data.email"
                            required 
                            autofocus
                            tabindex="1"
                            placeholder="Enter your email address"
                            class="w-full px-3 py-2 border border-[#418CED] rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:border-[#418CED] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200"
                        >
                        @error('data.email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Input --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="data.password"
                            wire:model="data.password"
                            required
                            tabindex="2"
                            placeholder="Enter your password"
                            class="w-full px-3 py-2 border border-[#418CED] rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:border-[#418CED] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200"
                        >
                        @error('data.password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me Checkbox --}}
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="data.remember"
                            wire:model="data.remember"
                            class="h-4 w-4 text-[#418CED] focus:ring-[#418CED] border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Remember me
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-[#418CED] rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:ring-offset-2 transition-colors duration-200"
                    >
                        Submit
                    </button>
                </form>

                @if(Route::has('filament.manpower.auth.password-reset.request'))
                <div class="mt-4 text-center">
                    <a href="{{ route('filament.manpower.auth.password-reset.request') }}"
                       class="text-sm font-medium text-[#418CED] hover:underline dark:text-blue-400">
                        Forgot your password?
                    </a>
                </div>
                @endif
                </div>
                
            </div>
        </div>
    </div>
</div>