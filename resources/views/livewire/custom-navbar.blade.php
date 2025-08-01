<div>
{{-- Header with Filament-style logout popup --}}
<div class="bg-[#418CED] w-full pt-4 space-y-4">
    <div class="flex items-center justify-between px-4">
        {{-- Logo --}}
        <div class="h-[32px] bg-white rounded-md border flex items-center px-4 py-4">
            <img class="h-[24px]" src="{{ asset('storage/project_logos/01K16C8M2CP2VWK1AFQ3M9GQMC.svg') }}" alt="Project Logo">
        </div>
        
        {{-- Logout Button --}}
        <div>
            <button
                onclick="showLogoutModal()"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md text-sm transition-colors duration-200 flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
            
            <form id="logout-form" action="{{ route('filament.manpower.auth.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    <div class="h-[20px] rounded-t-lg bg-white w-full"></div>
</div>

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
                    <h3 class="text-lg font-semibold text-gray-900">Confirm Logout</h3>
                    <p class="text-sm text-gray-500 mt-1">Are you sure you want to logout? You will need to sign in again to access the admin panel.</p>
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