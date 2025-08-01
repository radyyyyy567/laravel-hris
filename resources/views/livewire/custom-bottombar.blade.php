<div>
<div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-50 safe-area-pb pv-[40px] w-full">
    <div class="flex justify-between items-center py-2 px-4 w-full">
        @php
           $bottomNavItems = [
    [
        'label' => 'Home',
        'url' => '/manpower/dashboard-absence',
        'icon' => 'heroicon-o-home', // outline home icon
        'active' => true,
    ],
    [
        'label' => 'Jadwal',
        'url' => '/manpower/schedule',
        'icon' => 'heroicon-o-clock', // outline users icon
        'active' => false,
    ],
    [
        'label' => 'Riwayat',
        'url' => '/manpower/history-attedance',
        'icon' => 'heroicon-o-users', // outline report-like icon
        'active' => false,
    ],
    [
        'label' => 'Pengajuan',
        'url' => '#',
        'icon' => 'heroicon-o-users', // outline clipboard with check
        'active' => false,
    ],
    [
        'label' => 'Setting',
        'url' => '#',
        'icon' => 'heroicon-o-user-circle', // outline user-circle
        'active' => false,
    ],
];

        @endphp

        @foreach($bottomNavItems as $item)
            <a href="{{ $item['url'] }}" 
               class="flex flex-col items-center py-2 px-3 transition-colors
               {{ $item['active'] 
                   ? 'text-primary-600 dark:text-primary-400' 
                   : 'text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400' }}">
                 <x-dynamic-component :component="$item['icon']" class="h-6 w-6" />
                <span class="text-xs">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>

<style>
    .fi-main {
        padding-bottom: 80px !important;
    }
    .safe-area-pb {
        padding-bottom: env(safe-area-inset-bottom, 0px);
    }
    
    @media (min-width: 768px) {
        .mobile-bottom-bar {
            display: none;
        }
    }
</style>
</div>