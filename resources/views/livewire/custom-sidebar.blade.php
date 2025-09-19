<div>
    @if($user_role !== 'spv')  
<div class="relative mb-4" x-data="{ open: @entangle('isOpen') }">
    <!-- Dropdown Toggle Button -->
    <button wire:click="toggleDropdown" @click.away="open = false" @class([
        'fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 py-2 outline-none transition duration-75 border bg-white',
        'hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5',
        'w-full'
    ])>
        <div class="flex items-center gap-3 flex-1">
            @if($selectedProject)
                <x-dynamic-component :component="$selectedProject['icon']"
                    class="fi-sidebar-item-icon h-6 w-6 text-gray-400 dark:text-gray-500" />
                <span class="fi-sidebar-item-label flex-1 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ $selectedProject['label'] }}
                </span>
            @else
                <x-heroicon-o-folder class="fi-sidebar-item-icon h-6 w-6 text-gray-400 dark:text-gray-500" />
                <span class="fi-sidebar-item-label flex-1 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                    All Projects
                </span>
            @endif
        </div>

        <!-- Dropdown Arrow -->
        <x-heroicon-o-chevron-down
            class="h-4 w-4 text-gray-400 dark:text-gray-500 transition-transform duration-200"
            ::class="{ 'rotate-180': open }" />
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg ring-1 ring-black/5 dark:bg-gray-900 dark:border-gray-700 dark:ring-white/10"
        style="display: none;">
        <div class="py-1 max-h-60 overflow-y-auto">
            <!-- Add "All" option at the top -->
            <button wire:click="selectProject()" @class([
                'fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 py-2 outline-none transition duration-75',
                'w-full mx-1',
                'bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400' => $selectedProject === null,
                'text-gray-700 hover:bg-gray-100 focus-visible:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5 dark:focus-visible:bg-white/5' => $selectedProject !== null,
            ])>
                <!-- Icon -->
                <x-heroicon-o-square-3-stack-3d @class([
                    'fi-sidebar-item-icon h-6 w-6',
                    'text-primary-600 dark:text-primary-400' => $selectedProject === null,
                    'text-gray-400 dark:text-gray-500' => $selectedProject !== null,
                ]) />

                <!-- Content Area -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span @class([
                            'fi-sidebar-item-label text-sm font-medium truncate',
                            'text-primary-600 dark:text-primary-400' => $selectedProject === null,
                            'text-gray-700 dark:text-gray-200' => $selectedProject !== null,
                        ])>
                            All Projects
                        </span>
                    </div>
                </div>

                <!-- Active Indicator -->
                @if($selectedProject === null)
                    <x-heroicon-o-check class="h-4 w-4 text-primary-600 dark:text-primary-400 ml-2" />
                @endif
            </button>

            @forelse($projects as $project)
                <button wire:click="selectProject({{ $project['id'] }})" @class([
                    'fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 py-2 outline-none transition duration-75',
                    'w-full mx-1',
                    'bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400' => $project['active'],
                    'text-gray-700 hover:bg-gray-100 focus-visible:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5 dark:focus-visible:bg-white/5' => !$project['active'],
                ])>
                    <!-- Icon -->
                    <x-dynamic-component :component="$project['icon']" @class([
                        'fi-sidebar-item-icon h-6 w-6',
                        'text-primary-600 dark:text-primary-400' => $project['active'],
                        'text-gray-400 dark:text-gray-500' => !$project['active'],
                    ]) />

                    <!-- Content Area -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span @class([
                                'fi-sidebar-item-label text-sm font-medium truncate',
                                'text-primary-600 dark:text-primary-400' => $project['active'],
                                'text-gray-700 dark:text-gray-200' => !$project['active'],
                            ])>
                                {{ $project['label'] }}
                            </span>

                            <!-- Status Badge -->
                            @if(isset($project['status']))
                                <span @class([
                                    'inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium leading-4 rounded-md ml-2',
                                    'bg-success-50 text-success-700 ring-1 ring-success-600/20 dark:bg-success-400/10 dark:text-success-400 dark:ring-success-400/20' => $project['status'] === 'Done',
                                    'bg-primary-50 text-primary-700 ring-1 ring-primary-600/20 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/20' => $project['status'] === 'Work',
                                    'bg-gray-50 text-gray-700 ring-1 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20' => !in_array($project['status'], ['Done', 'Work']),
                                ])>
                                    {{ ucfirst($project['status']) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Active Indicator -->
                    @if($project['active'])
                        <x-heroicon-o-check class="h-4 w-4 text-primary-600 dark:text-primary-400 ml-2" />
                    @endif
                </button>
            @empty
                <div class="px-3 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <x-heroicon-o-folder-open class="h-5 w-5" />
                        <span>No projects available</span>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endif

@php
    // Helper function to build URLs with proper project parameter handling
    function buildRoute($routeName, $selectedProject = null) {
        try {
            switch ($routeName) {
                case 'edit_project':
                    // For edit_project, use the selected project ID or get current project from request
                    $projectId = $selectedProject['id'] ?? request()->route('record') ?? $GLOBALS['project_global'] ?? null;
                    
                    if ($projectId) {
                        return "/admin/projects/{$projectId}/edit";
                    } else {
                        // If no project selected, redirect to projects index
                        return route('filament.admin.resources.projects.index');
                    }
                    break;
                    
                case 'filament.admin.resources.projects.index':
                    return route($routeName);
                    break;
                    
                default:
                    // Check if route exists before trying to generate URL
                    if (!Route::has($routeName)) {
                        return '#';
                    }
                    return route($routeName);
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::warning("Route building failed for {$routeName}: " . $e->getMessage());
            return '#';
        }
    }
    
    // Helper function to check if user has access to menu item
    function hasAccess($allowedRoles, $userRole) {
        if (empty($allowedRoles)) return true;
        return in_array($userRole, $allowedRoles);
    }
    
    // Enhanced navigation structure with role-based access control
    $navigationGroups = [
        [
            'label' => null,
            'collapsible' => false,
            'defaultOpen' => true,
            'allowedRoles' => [],
            'items' => [
                [
                    'name' => 'Dashboard',
                    'route' => 'filament.admin.pages.dashboard',
                    'icon' => 'heroicon-o-squares-2x2',
                    'badge' => null,
                    'activeOn' => ['filament.admin.pages.dashboard'],
                    'allowedRoles' => ['super_admin', 'admin'],
                    'requiresProject' => false,
                ],
            ]
        ],
        [
            'label' => null,
            'collapsible' => false,
            'defaultOpen' => true,
            'allowedRoles' => ['super_admin', 'admin'],
            'items' => [
                [
                    'name' => 'Spv',
                    'route' => 'filament.admin.resources.spvs.index',
                    'icon' => 'heroicon-o-squares-2x2',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.spvs.*'],
                    'allowedRoles' => ['super_admin', 'admin', 'spv'],
                    'requiresProject' => false,
                ],
            ]
        ],
        [
            'label' => null,
            'collapsible' => false,
            'defaultOpen' => true,
            'allowedRoles' => ['super_admin', 'admin', 'spv'],
            'items' => [
                [
                    'name' => 'Man power',
                    'route' => 'filament.admin.resources.manpowers.index',
                    'icon' => 'heroicon-o-squares-2x2',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.manpowers.*'],
                    'allowedRoles' => ['super_admin', 'admin', 'spv'],
                    'requiresProject' => false,
                ],
            ]
        ],
        [
            'label' => 'Attendance',
            'collapsible' => true,
            'defaultOpen' => false,
            'icon' => 'heroicon-o-cog-6-tooth',
            'allowedRoles' => ['super_admin', 'admin', 'spv'],
            'items' => [
                [
                    'name' => 'Jadwal Attendance',
                    'route' => 'filament.admin.resources.schedule-absences.index',
                    'icon' => 'heroicon-o-calendar',
                    'badge' => $pendingAbsences ?? null,
                    'activeOn' => ['filament.admin.resources.schedule-absences.*'],
                    'allowedRoles' => ['super_admin', 'admin'],
                    'requiresProject' => false,
                ],
                [
                    'name' => 'Data Attendance',
                    'route' => 'filament.admin.resources.absences.index',
                    'icon' => 'heroicon-o-user-group',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.absences.*'],
                    'allowedRoles' => ['super_admin', 'admin', 'spv'],
                    'requiresProject' => false,
                ],
                [
                    'name' => 'Rekap Attendance',
                    'route' => 'filament.admin.resources.recap-absences.index',
                    'icon' => 'heroicon-o-briefcase',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.recap-absences.*'],
                    'allowedRoles' => ['super_admin', 'admin', 'spv'],
                    'requiresProject' => false,
                ],
            ]
        ],
        [
            'label' => 'Pengajuan',
            'collapsible' => true,
            'defaultOpen' => false,
            'icon' => 'heroicon-o-cog-6-tooth',
            'allowedRoles' => ['super_admin', 'admin', 'spv'],
            'items' => [
                [
                    'name' => 'Daftar Pengajuan',
                    'route' => 'filament.admin.resources.overtime-assignments.index',
                    'icon' => 'heroicon-o-calendar',
                    'badge' => $pendingAbsences ?? null,
                    'activeOn' => ['filament.admin.resources.overtime-assignments.*'],
                    'allowedRoles' => ['super_admin', 'admin', 'spv'],
                    'requiresProject' => false,
                ],
                [
                    'name' => 'Riwayat Pengajuan',
                    'route' => 'filament.admin.resources.history-submissions.index',
                    'icon' => 'heroicon-o-user-group',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.history-submissions.*'],
                    'allowedRoles' => ['super_admin', 'admin', 'spv'],
                    'requiresProject' => false,
                ],
                [
                    'name' => 'Pengajuan Hold',
                    'route' => 'filament.admin.resources.hold-submissions.index',
                    'icon' => 'heroicon-o-briefcase',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.hold-submissions.*'],
                    'allowedRoles' => ['super_admin', 'admin', 'spv'],
                    'requiresProject' => false,
                ],
            ]
        ],
        [
            'label' => 'Proyek',
            'collapsible' => true,
            'defaultOpen' => false,
            'icon' => 'heroicon-o-building-office-2',
            'allowedRoles' => ['super_admin'],
            'items' => [
                [
                    'name' => 'Penempatan Proyek',
                    'route' => 'filament.admin.resources.placements.index',
                    'icon' => 'heroicon-o-building-office',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.placements.*'],
                    'allowedRoles' => ['super_admin', 'admin'],
                    'requiresProject' => false,
                ],
                [
                    'name' => 'Pengaturan Proyek',
                    'route' => 'edit_project',
                    'icon' => 'heroicon-o-building-office',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.projects.edit'],
                    'allowedRoles' => ['super_admin', 'admin'],
                    'requiresProject' => true,
                ],
            ]
        ],
        [
            'label' => null,
            'collapsible' => false,
            'defaultOpen' => true,
            'allowedRoles' => ['super_admin', 'admin'],
            'items' => [
                [
                    'name' => 'Data Proyek',
                    'route' => 'filament.admin.resources.projects.index',
                    'icon' => 'heroicon-o-squares-2x2',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.projects.*'],
                    'allowedRoles' => ['super_admin', 'admin'],
                    'requiresProject' => false,
                ],
            ]
        ],
       
        [
            'label' => null,
            'collapsible' => false,
            'defaultOpen' => true,
            'allowedRoles' => ['super_admin'],
            'items' => [
                [
                    'name' => 'Data Pengguna',
                    'route' => 'filament.admin.resources.users.index',
                    'icon' => 'heroicon-o-squares-2x2',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.users.*'],
                    'allowedRoles' => ['super_admin'],
                    'requiresProject' => false,
                ],
            ]
        ],
        [
            'label' => null,
            'collapsible' => false,
            'defaultOpen' => true,
            'allowedRoles' => [],
            'items' => [
                [
                    'name' => 'Notifikasi',
                    'route' => 'filament.admin.resources.notifications.index',
                    'icon' => 'heroicon-o-bell',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.notifications.*'],
                    'allowedRoles' => [],
                    'requiresProject' => false,
                ],
            ]
        ],
         [
            'label' => null,
            'collapsible' => false,
            'defaultOpen' => true,
            'allowedRoles' => ['super_admin'],
            'items' => [
                [
                    'name' => 'Admin',
                    'route' => 'filament.admin.resources.users.index',
                    'icon' => 'heroicon-o-user',
                    'badge' => null,
                    'activeOn' => ['filament.admin.resources.users.*'],
                    'allowedRoles' => ['super_admin',],
                    'requiresProject' => false,
                ],
            ]
        ],
    ];
    
    // Filter navigation groups based on user role
    $filteredNavigationGroups = [];
    foreach ($navigationGroups as $group) {
        if (!hasAccess($group['allowedRoles'], $user_role)) {
            continue;
        }
        
        $filteredItems = [];
        foreach ($group['items'] as $item) {
            if (hasAccess($item['allowedRoles'], $user_role)) {
                // Check if item requires project and if project is selected
                if (isset($item['requiresProject']) && $item['requiresProject'] && !$selectedProject) {
                    // Skip items that require project when no project is selected
                    continue;
                }
                $filteredItems[] = $item;
            }
        }
        
        if (!empty($filteredItems)) {
            $group['items'] = $filteredItems;
            $filteredNavigationGroups[] = $group;
        }
    }
@endphp

<!-- Navigation Groups with Role-Based Access Control -->
<nav class="fi-sidebar-nav flex flex-col gap-y-2">
    @foreach ($filteredNavigationGroups as $groupIndex => $group)
        @php
            // Check if any item in the group is active
            $hasActiveItem = false;
            foreach($group['items'] as $item) {
                foreach($item['activeOn'] as $pattern) {
                    if(request()->routeIs($pattern)) {
                        $hasActiveItem = true;
                        break 2;
                    }
                }
            }
        @endphp
        
        <div @class([
            'fi-sidebar-group flex flex-col',
            'pt-2' => $groupIndex > 0 && $group['label']
        ])>
            @if($group['label'])
                @if($group['collapsible'])
                    <!-- Collapsible Group Header -->
                    <div x-data="{ open: {{ $group['defaultOpen'] || $hasActiveItem ? 'true' : 'false' }} }">
                        <button @click="open = !open" @class([
                            'fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg py-2 p-2 -mx-2 outline-none transition duration-75 w-full',
                            'hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5',
                            'bg-gray-50 dark:bg-white/5' => $hasActiveItem,
                        ])>
                            @if(isset($group['icon']))
                                <x-dynamic-component :component="$group['icon']" @class([
                                    'fi-sidebar-item-icon',
                                    'text-primary-600 dark:text-primary-400' => $hasActiveItem,
                                    'text-gray-400 dark:text-gray-500' => !$hasActiveItem,
                                ]) style="width: 24px; height: 24px" />
                            @endif
                            
                            <span @class([
                                'fi-sidebar-item-label flex-1 text-left text-sm font-medium',
                                'text-primary-600 dark:text-primary-400' => $hasActiveItem,
                                'text-gray-700 dark:text-gray-200' => !$hasActiveItem,
                            ])>
                                {{ $group['label'] }}
                            </span>

                            <x-heroicon-o-chevron-down @class([
                                'h-4 w-4 transition-transform duration-200',
                                'text-primary-600 dark:text-primary-400' => $hasActiveItem,
                                'text-gray-400 dark:text-gray-500' => !$hasActiveItem,
                            ]) 
                            x-bind:class="{ 'rotate-180': open }" />
                        </button>

                        <!-- Collapsible Group Items -->
                        <div x-show="open" 
                             x-transition:enter="transition duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-4 mt-1 space-y-1">
                            @foreach($group['items'] as $item)
                                @php
                                    $isActive = false;
                                    foreach($item['activeOn'] as $pattern) {
                                        if(request()->routeIs($pattern)) {
                                            $isActive = true;
                                            break;
                                        }
                                    }
                                    
                                    // Build URL with proper project handling
                                    $itemUrl = buildRoute($item['route'], $selectedProject);
                                @endphp
                                
                                @if($itemUrl !== '#')
                                    <a href="{{ $itemUrl }}" @class([
                                        'fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg py-2 outline-none transition duration-75',
                                        'fi-active fi-sidebar-item-active bg-gray-100 dark:bg-white/5' => $isActive,
                                        'hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5' => !$isActive,
                                    ])>
                                        <x-dynamic-component :component="$item['icon']" @class([
                                            'fi-sidebar-item-icon h-[24px] w-[24px]',
                                            'text-primary-600 dark:text-primary-400' => $isActive,
                                            'text-gray-400 dark:text-gray-500' => !$isActive,
                                        ]) />
                                        
                                        <span @class([
                                            'fi-sidebar-item-label flex-1 text-sm font-medium',
                                            'text-primary-600 dark:text-primary-400' => $isActive,
                                            'text-gray-700 dark:text-gray-200' => !$isActive,
                                        ])>
                                            {{ $item['name'] }}
                                        </span>

                                        @if($item['badge'])
                                            <span class="fi-sidebar-item-badge inline-flex items-center justify-center min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight bg-gray-100 text-gray-800 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                                {{ $item['badge'] }}
                                            </span>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            @if(!$group['collapsible'])
                <!-- Non-collapsible Group Items -->
                <ul class="fi-sidebar-nav-groups flex flex-col gap-y-1">
                    @foreach($group['items'] as $item)
                        @php
                            $isActive = false;
                            foreach($item['activeOn'] as $pattern) {
                                if(request()->routeIs($pattern)) {
                                    $isActive = true;
                                    break;
                                }
                            }
                            
                            // Build URL with proper project handling
                            $itemUrl = buildRoute($item['route'], $selectedProject);
                        @endphp
                        
                        @if($itemUrl !== '#')
                            <li class="fi-sidebar-nav-item">
                                <a href="{{ $itemUrl }}" @class([
                                    'fi-sidebar-item-button relative flex items-center justify-center gap-x-3 rounded-lg px-2 -mx-2 py-2 outline-none transition duration-75',
                                    'fi-active fi-sidebar-item-active bg-gray-100 dark:bg-white/5' => $isActive,
                                    'hover:bg-gray-100 focus-visible:bg-gray-100 dark:hover:bg-white/5 dark:focus-visible:bg-white/5' => !$isActive,
                                ])>
                                    <x-dynamic-component :component="$item['icon']" @class([
                                        'fi-sidebar-item-icon h-[24px] w-[24px]',
                                        'text-primary-600 dark:text-primary-400' => $isActive,
                                        'text-gray-400 dark:text-gray-500' => !$isActive,
                                    ]) />
                                    
                                    <span @class([
                                        'fi-sidebar-item-label flex-1 text-sm font-medium',
                                        'text-primary-600 dark:text-primary-400' => $isActive,
                                        'text-gray-700 dark:text-gray-200' => !$isActive,
                                    ])>
                                        {{ $item['name'] }}
                                    </span>

                                    @if($item['badge'])
                                        <span class="fi-sidebar-item-badge inline-flex items-center justify-center min-h-4 px-2 py-0.5 text-xs font-medium tracking-tight bg-gray-100 text-gray-800 rounded-md dark:bg-gray-800 dark:text-gray-200">
                                            {{ $item['badge'] }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach
</nav>
</div>