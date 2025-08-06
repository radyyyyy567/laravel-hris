<div class="fixed inset-0 z-[60] overflow-auto">
    <div class="flex min-h-full items-center justify-center bg-gray-50 dark:bg-gray-900">
        {{-- YOUR CUSTOM HEADER AND FORM --}}
        <div class="w-full bg-[#418CED] h-screen flex-col items-center flex justify-end space-y-10">
            <div class="">
                <img src="{{ asset(path: 'assets/bg-login.svg') }}" alt="Login Background"
                    class="h-full w-full object-cover">
            </div>
            {{-- Login Form --}}
            <div
                class="rounded-[20px] bg-white pb-[40px] p-[20px] shadow dark:bg-gray-800 rounded-b-none w-full flex justify-center">
                <div class="w-[412px] space-y-[30px]">
                    <div class="mb-8 text-center py-[20px] px-[10px] space-y-[10px]">
                        <p class="mt-2 text-2xl font-bold text-blue-500 dark:text-gray-400">
                            Login
                        </p>
                        <p class="text-base text-[#555]">
                            Silahkan masuk menggunakan akun anda.
                        </p>
                    </div>


                    <form wire:submit="authenticate" class="space-y-[10px]">
                        @csrf

                        {{-- Email Input --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email / NIP
                            </label>
                            <input type="email" id="email" name="data.email" wire:model="data.email" required autofocus
                                tabindex="1" placeholder="Enter your email or employee code"
                                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]">
                            @error('data.email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password Input --}}
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Password
                            </label>
                            <input type="password" id="password" name="data.password" wire:model="data.password"
                                required tabindex="2" placeholder="Enter your password"
                                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]">
                            @error('data.password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full items-end">
                            <a href="#" class="text-sm text-[#555]">Forgot password?</a>
                        </div>
                      
                        {{-- Submit Button --}}
                        <button type="submit"
                            class="flex items-center justify-center w-full px-[15px] py-[20px] text-sm font-bold text-white bg-[#418CED] rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:ring-offset-2 transition-colors duration-200">
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