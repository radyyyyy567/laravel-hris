@php
    use Illuminate\Support\Facades\Request;

    $bottomNavItems = [
        [
            'label' => 'Home',
            'url' => '/manpower/dashboard-absence',
            'icon' => 'phosphor-house-fill',
            'icon-inactive' => 'phosphor-house'
        ],
        [
            'label' => 'Jadwal',
            'url' => '/manpower/schedule',
            'icon' => 'phosphor-clock-user-fill',
            'icon-inactive' => 'phosphor-clock-user'
        ],
        [
            'label' => 'Riwayat',
            'url' => '/manpower/history-attedance',
            'icon' => 'phosphor-scroll-fill',
            'icon-inactive' => 'phosphor-scroll'
        ],
        [
            'label' => 'Pengajuan',
            'url' => '/manpower/submission',
            'icon' => 'phosphor-file-plus-fill',
            'icon-inactive' => 'phosphor-file-plus'
        ],
        [
            'label' => 'Settings',
            'url' => '/manpower/settings',
            'icon' => 'phosphor-gear-fill',
            'icon-inactive' => 'phosphor-gear'
        ],
    ];
@endphp

<div class="fixed pt-[8px] bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 z-50 safe-area-pb pv-[40px] w-full">
    <div class="flex justify-between items-center py-2 px-4 w-full">
        @foreach($bottomNavItems as $item)
            @php
                $isActive = Request::is(ltrim($item['url'], '/'));
            @endphp

            <a href="{{ $item['url'] }}" 
               class="flex flex-col items-center py-2 px-3 transition-colors
               {{ $isActive 
                   ? 'text-[#418CED] dark:text-primary-400' 
                   : 'text-gray-600 hover:text-[#418CED] ' }}">
              
                <x-dynamic-component 
    :component="$isActive ? $item['icon'] : $item['icon-inactive']" 
    class="h-6 w-6 mb-[10px]" 
/>
                
                
                    <span class="block 
                    {{ $isActive ? 'bg-blue-600' : 'bg-transparent' }}                    
                    w-8 h-1 bg-blue-600  rounded-full"></span>
            </a>
        @endforeach
    </div>
</div>
