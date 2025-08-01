<div>
    <button 
        wire:click="confirmLogout"
        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md text-sm"
    >
        Logout
    </button>

    <script>
        window.addEventListener('confirm-logout', () => {
            window.Filament?.notification?.confirm({
                title: 'Logout',
                description: 'Are you sure you want to logout?',
                icon: 'heroicon-o-question-mark-circle',
                acceptLabel: 'Yes, Logout',
                rejectLabel: 'Cancel',
                onAccept: () => {
                    @this.call('logout');
                }
            });
        });
    </script>
</div>
