<div class="mb-[80px]">
    <form wire:submit.prevent="save" class="space-y-6 mx-auto rounded-md">
        {{-- NIP --}}
        <div>
            <label class="block text-sm text-gray-400 mb-1">NIP</label>
            <input type="text" value="{{ $nip }}" disabled
                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]" />
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm text-gray-400 mb-1">Email</label>
            <input type="email" wire:model.defer="email"
                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]" />
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Nama Lengkap --}}
        <div>
            <label class="block text-sm text-gray-400 mb-1">Nama Lengkap</label>
            <input type="text" wire:model.defer="name"
                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]" />
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Jabatan --}}
        <div>
            <label class="block text-sm text-gray-400 mb-1">Jabatan</label>
            <select wire:model.defer="group_id" disabled
                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]">
                <option value="">-- Pilih Jabatan --</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
            @error('group_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Project --}}
        <div>
            <label class="block text-sm text-gray-400 mb-1">Project</label>
            <input type="text" value="{{ $project_name }}" disabled
                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]" />
        </div>

        {{-- Penempatan --}}
        <div>
            <label class="block text-sm text-gray-400 mb-1">Penempatan</label>
            <input type="text" value="{{ $placement }}" disabled
                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]" />
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm text-gray-400 mb-1">Password</label>
            <div class="relative" x-data="{ show: false }">
                <input :type="show ? 'text' : 'password'" wire:model.defer="password"
                    placeholder="Leave blank to keep current password"
                    class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0] pr-10" />
                <button type="button" @click="show = !show"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500">
                    👁️
                </button>
            </div>
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Buttons --}}
        <div class="grid grid-cols-2 justify-end gap-4 pt-4 w-full">
            <button type="button" class="px-6 py-2 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-50 w-full">
                Batal
            </button>
            <button type="submit" class="flex items-center justify-center w-full px-[15px] py-[20px] text-sm font-bold text-white bg-[#418CED] rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:ring-offset-2 transition-colors duration-200">
                Simpan
            </button>
        </div>
    </form>
 <button
                onclick="showLogoutModal()"
                class="mt-4 w-full px-[15px] py-[20px] flex justify-center font-semibold bg-red-500 hover:bg-red-600 text-white rounded-md text-sm transition-colors duration-200 flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>

            {{-- Filament-style Modal --}}
<div id="logout-modal" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="hideLogoutModal()"></div>
    
    {{-- Modal --}}
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full transform transition-all">
            {{-- Modal Header --}}
            <div class="flex items-center gap-4 p-6 border-b border-gray-200">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Keluar</h3>
                    <p class="text-sm text-gray-500 mt-1">Apakah kamu yakin ingin keluar? kamu perlu login lagi jika ingin mengakses halaman ini.</p>
                </div>
            </div>
            
            {{-- Modal Actions --}}
            <div class="flex justify-between gap-3 p-6 bg-gray-50 rounded-b-xl">
                <button
                    onclick="hideLogoutModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                >
                    Cancel
                </button>
                <button
                    onclick="confirmLogout()"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                >
                    Yes, Logout
                </button>
            </div>
        </div>
    </div>
</div>
    @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <script>
function showLogoutModal() {
    const modal = document.getElementById('logout-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Add animation
    setTimeout(() => {
        modal.querySelector('.bg-black\\/50').classList.add('opacity-100');
        modal.querySelector('.transform').classList.add('scale-100');
    }, 10);
}

function hideLogoutModal() {
    const modal = document.getElementById('logout-modal');
    modal.querySelector('.bg-black\\/50').classList.remove('opacity-100');
    modal.querySelector('.transform').classList.remove('scale-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

function confirmLogout() {
    // Show loading state
    const confirmBtn = document.querySelector('[onclick="confirmLogout()"]');
    confirmBtn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Logging out...
    `;
    confirmBtn.disabled = true;
    
    // Submit logout form
    document.getElementById('logout-form').submit();
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideLogoutModal();
    }
});
</script>

<style>
.bg-black\/50 {
    background-color: rgba(0, 0, 0, 0.5);
}

.transform {
    transform: scale(0.95);
    opacity: 0;
}

.scale-100 {
    transform: scale(1);
    opacity: 1;
}

.opacity-100 {
    opacity: 1;
}

/* Smooth transitions */
.bg-black\/50,
.transform {
    transition: all 0.2s ease-out;
}
</style>
</div>


