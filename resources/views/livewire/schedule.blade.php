<div class="space-y-4 max-h-screen overflow-auto px-4 -mx-4">
    @foreach($scheduleData as $schedule)
        <div class="bg-[#F9F9F9] rounded-lg shadow p-4">
            <div class="space-y-2">
                <div class="grid grid-cols-2">
                    <div class="space-y-2">
                        <div class="flex item-center space-x-4">

                            <div class="h-[36px] w-[36px] justify-center bg-white flex items-center rounded-lg">
                                <x-dynamic-component :component="'heroicon-o-calendar'"
                                    class="w-[16px] h-[16px] text-blue-600" />
                            </div>
                            <p class="text-gray-900 text-[24px] font-semibold">{{ $schedule->date->format('d, M') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-900">Check In</p>
                            <p class="text-gray-900 font-bold text-[24px]">{{ $schedule->checkin_time }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex item-center space-x-4">
                            <div class="h-[36px] w-[36px] justify-center bg-white flex items-center rounded-lg">
                                <x-dynamic-component :component="'heroicon-o-identification'"
                                    class="w-[16px] h-[16px] text-blue-600" />
                            </div>
                            <p class="text-gray-900 text-[24px] font-semibold">{{ $schedule->status }}</p>
                        </div>
                         <div>
                            <p class="text-gray-900">Check Out</p>
                            <p class="text-gray-900 font-bold text-[24px]">{{ $schedule->checkout_time }}</p>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    @endforeach
</div>