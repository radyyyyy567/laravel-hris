<div class="space-y-4 text-[#555] max-h-screen overflow-auto px-4 -mx-4 -mt-[20px] pb-[100px]">

    <div class="text-[#555]">{{ $type === 'schedule' ? 'Jadwal Absen' : 'Riwayat'}}</div>
    @foreach($scheduleData as $schedule)
        <div class="bg-[#f2f2f2] rounded-lg  p-[10px]">
            <div class="space-y-2">
                <div class="grid grid-cols-2">
                    <div class="space-y-2">
                        <div class="flex item-center space-x-[10px]">
                            <div class="p-[10px] justify-center bg-white flex items-center rounded-[10px]">
                                <x-dynamic-component :component="'phosphor-calendar-blank'"
                                    class="w-[16px] h-[16px] text-blue-600" />
                            </div>
                            <div class="text-gray-[#555]  text-[20px] font-bold flex items-center">
                                <div>{{ $schedule->date->format('d, M') }}</div>
                            </div>
                        </div>
                        <div>
                            <p class="text-[#555]">Clock In</p>
                            <p class="text-[#555] font-bold text-xl">{{ $schedule->checkin_time }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex space-x-2  rounded-lg items-center">
                            <div class="  space-x-2 bg-white p-[10px] rounded-[10px] flex items-center">
                                <x-dynamic-component :component="'phosphor-identification-badge'"
                                    class="w-[16px] h-[16px] text-blue-600" />
                            </div>
                            <p class="text-[#555] text-[20px] font-semibold">{{ $schedule->status }}</p>
                        </div>
                        
                        <div>
                            <p class="text-[#555]">Clock Out</p>
                            <p class="text-[#555] font-bold text-xl">{{ $schedule->checkout_time }}</p>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    @endforeach
</div>