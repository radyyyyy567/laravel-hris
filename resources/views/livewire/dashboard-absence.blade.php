<div>
    <div>
        @php
            $stats = [
                [
                    'title' => 'Absen Tanggal',
                    'value' => $schedule_today?->absence_date ? \Carbon\Carbon::parse($schedule_today->absence_date)->locale('id')->translatedFormat('d F') : '-',
                    'icon' => 'heroicon-o-calendar',
                    'description' => '',
                ],
                [
                    'title' => 'Jenis Absen',
                    'value' => $schedule_today['status'] ?? '-',
                    'icon' => 'heroicon-o-user',
                    'description' => '',
                ],
                [
                    'title' => 'Check-in',
                    'value' => $schedule_today?->checkin_time
                        ? \Carbon\Carbon::parse($schedule_today->checkin_time)->format('H:i')
                        : '-',
                    'icon' => 'heroicon-o-arrow-right-end-on-rectangle',
                    'description' => '',
                ],
                [
                    'title' => 'Check-out',
                    'value' => $schedule_today?->checkout_time
                        ? \Carbon\Carbon::parse($schedule_today->checkout_time)->format('H:i')
                        : '-',
                    'icon' => 'heroicon-o-arrow-right-start-on-rectangle',
                    'description' => '',
                ],
            ];

            $absence_status = [
                [
                    'title' => 'Check-in',
                    'value' => $absence?->checkin_time
                        ? \Carbon\Carbon::parse($absence->checkin_time)->format('H:i')
                        : '-',
                    'icon' => 'heroicon-o-arrow-right-end-on-rectangle',
                    'description' => '',
                ],
                [
                    'title' => 'Check-out',
                    'value' => $absence?->checkout_time
                        ? \Carbon\Carbon::parse($absence->checkout_time)->format('H:i')
                        : '-',
                    'icon' => 'heroicon-o-arrow-right-start-on-rectangle',
                    'description' => '',
                ],

            ];
        @endphp

        <!-- Flash message display -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        <div>
            <div class="text-[16px]">Selamat Datang,</div>
            <div class="font-bold text-[42px]">{{ $name }}</div>
        </div>


        <div class="text-sm text-[#555] mb-2">
            Jadwal Hari ini
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
            @foreach ($stats as $stat)
                <x-filament::card>
                    <div class="-m-6 p-[20px]">
                        <div class="space-x-2 flex items-center">
                            <div class="shadow rounded-lg bg-white ">
                                <x-dynamic-component :component="$stat['icon']" class="h-[16px] w-[16px] text-blue-600" />
                            </div>

                            <h2 class="text-[14px]  font-bold">{{ $stat['title'] }}</h2>
                        </div>

                        <p class="text-2xl font-semibold text-[#555]">{{ $stat['value'] }}</p>
                        <p class="text-sm text-gray-500">{{ $stat['description'] }}</p>
                    </div>
                </x-filament::card>
            @endforeach
        </div>


       <div class="text-sm text-[#555] mb-2">
                Status
            </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($absence_status as $stat)
                <x-filament::card>
                    <div class="-m-6 p-[20px]">
                        <div class="space-x-2 flex items-center">
                            <div class="shadow rounded-lg bg-white ">
                                <x-dynamic-component :component="$stat['icon']" class="h-[16px] w-[16px] text-blue-600" />
                            </div>

                            <h2 class="text-[14px]  font-bold">{{ $stat['title'] }}</h2>
                        </div>

                        <p class="text-2xl font-semibold text-[#555]">{{ $stat['value'] }}</p>
                        <p class="text-sm text-gray-500">{{ $stat['description'] }}</p>
                    </div>
                </x-filament::card>
            @endforeach
        </div>



        <div class="max-w-sm mx-auto mt-10">
            <div class="text-sm text-[#555] mb-2">
                Action
            </div>
            @if ($absence?->checkin_time && $absence?->checkout_time)
                <div class="text-sm text-gray-500 mb-4">
                    Anda sudah melakukan Check-in dan Check-out hari ini.
                </div>
            @elseif ($schedule_today == null)
                {{-- Show Checkout Slider --}}
                <div  class="relative bg-gray-500 h-[76px] rounded-[20px] overflow-hidden select-none ">
                    <div id="sliderLabel"
                        class="absolute inset-0 flex items-center justify-center text-white font-semibold pointer-events-none text-sm">
                        Tidak ada jadwal hari ini
                    </div>
                    <div class="h-full flex items-center  absolute px-4" >
                        <div
                            class=" w-[36px] h-[36px] bg-white rounded-[10px] shadow-md cursor-pointer flex items-center justify-center transition-transform duration-100 z-10">
                            <x-dynamic-component :component="'heroicon-o-x-circle'" class="h-4 w-4 text-red-600" />

                        </div>
                    </div>

                </div>
            @elseif ($absence?->checkin_time && !$absence?->checkout_time)
                {{-- Show Checkout Slider --}}
                <div id="sliderTrack" class="relative bg-[#418CED] h-[76px] rounded-[20px] overflow-hidden select-none ">
                    <div id="sliderLabel"
                        class="absolute inset-0 flex items-center justify-center text-white font-semibold pointer-events-none text-sm">
                        Geser untuk foto dan Check-out
                    </div>
                    <div class="h-full flex items-center  absolute px-4" id="sliderBtn">
                        <div
                            class=" w-[36px] h-[36px] bg-white rounded-[10px] shadow-md cursor-pointer flex items-center justify-center transition-transform duration-100 z-10">
                            <x-dynamic-component :component="'heroicon-o-paper-airplane'" class="h-4 w-4 text-blue-600" />

                        </div>
                    </div>

                </div>
            @else
                {{-- Show Check-in Slider --}}
                <div id="sliderTrack" class="relative bg-[#418CED] h-[76px] rounded-[20px] overflow-hidden select-none ">
                    <div id="sliderLabel"
                        class="absolute inset-0 flex items-center justify-center text-white font-semibold pointer-events-none text-sm">
                        Geser untuk foto dan Check-in
                    </div>
                    <div class="h-full flex items-center  absolute px-4" id="sliderBtn">
                        <div
                            class=" w-[36px] h-[36px] bg-white rounded-[10px] shadow-md cursor-pointer flex items-center justify-center transition-transform duration-100 z-10">
                            <x-dynamic-component :component="'heroicon-o-paper-airplane'" class="h-4 w-4 text-blue-600" />

                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>

    <style>
        #sliderTrack {
            touch-action: pan-y;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        const isCheckin = "{{ $absence?->checkin_time && !$absence?->checkout_time ? '0' : '1' }}";

        $(function () {
            const $btn = $('#sliderBtn');
            const $track = $('#sliderTrack');
            const maxDrag = $track.width() - $btn.outerWidth(true) - 4;
            let isDragging = false;
            let startX = 0;

            $btn.on('mousedown touchstart', function (e) {
                isDragging = true;
                startX = e.pageX || e.originalEvent.touches[0].pageX;
                e.preventDefault();
            });

            $(document).on('mousemove touchmove', function (e) {
                if (!isDragging) return;
                const moveX = e.pageX || e.originalEvent.touches[0].pageX;
                let diff = moveX - startX;
                diff = Math.max(0, Math.min(diff, maxDrag));
                $btn.css('transform', `translateX(${diff}px)`);
                e.preventDefault();
            });

            $(document).on('mouseup touchend', function () {
                if (!isDragging) return;
                isDragging = false;

                const currentLeft = $btn.offset().left - $track.offset().left;
                if (currentLeft >= maxDrag - 10) {
                    $('#sliderLabel').text('✅ Absensi Terkirim');
                    $btn.css('transform', `translateX(${maxDrag}px)`);
                    $btn.off(); // prevent further drag

                    // Get GPS and submit to Livewire
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            const lat = position.coords.latitude;
                            const long = position.coords.longitude;
                            console.log(`Lat: ${lat}, Long: ${long}`);

                            if (isCheckin === '1') {
                                @this.call('submitCheckin', lat, long);
                            } else {
                                @this.call('submitCheckout', lat, long);
                            }
                        }, function (err) {
                            alert('Gagal mengambil lokasi: ' + err.message);

                            if (isCheckin === '1') {
                                @this.call('submitCheckin');
                            } else {
                                @this.call('submitCheckout');
                            }
                        });
                    }
                } else {
                    // Reset position
                    $btn.css('transform', `translateX(0px)`);
                }
            });

            // Listen for Livewire events
            Livewire.on('absenceSubmitted', () => {
                console.log('Absence submitted successfully');
            });
        });
    </script>
</div>