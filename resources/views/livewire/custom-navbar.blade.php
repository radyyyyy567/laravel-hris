<div>
{{-- Header with Filament-style logout popup --}}
<div class="bg-[#418CED] w-full h-[116px]">
    <div class="flex items-center justify-between p-[20px] pt-[30px] ">
        {{-- Logo --}}
        <a href="/">
        <div class="">
            <img class="h-[30px]" src="{{ asset('storage/' . $user_data?->manpower[0]?->project->logo) }}" alt="Project Logo">

        </div>
        </a>
        
        {{-- Logout Button --}}
        
        <div class="flex items-center space-x-2">
            <div>
                <a href="/manpower/notifications">
                    <x-dynamic-component :component="'heroicon-s-bell'" class="text-white h-6 w-6"/>
                </a>
            </div>
           
            
            <form id="logout-form" action="{{ route('filament.manpower.auth.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    <div class="h-[20px] mt-[16px] rounded-t-[20px] bg-white w-full"></div>
</div>




</div>