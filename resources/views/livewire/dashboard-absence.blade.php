<div>
    <div class="pb-[100px]">
   @php
    $stats = [
        [
            'title' => 'Tanggal',
            'value' => $schedule_today?->absence_date ? \Carbon\Carbon::parse($schedule_today->absence_date)->locale('id')->translatedFormat('d, M') : '-',
            'icon' => 'phosphor-calendar-blank',
            'description' => '',
        ],
        [
            'title' => 'Jenis Absen',
            'value' => $schedule_today['status'] ?? '-',
            'icon' => 'phosphor-identification-badge',
            'description' => '',
        ],
        [
            'title' => 'Check-in',
            'value' => $schedule_today?->checkin_time
                ? \Carbon\Carbon::parse($schedule_today->checkin_time)->format('H:i')
                : '-',
            'icon' => 'phosphor-sign-in-bold',
            'description' => '',
        ],
        [
            'title' => 'Check-out',
            'value' => $schedule_today?->checkout_time
                ? \Carbon\Carbon::parse($schedule_today->checkout_time)->format('H:i')
                : '-',
            'icon' => 'phosphor-sign-out-bold',
            'description' => '',
        ],
    ];

    $absence_status = [
        [
            'title' => 'Check-in',
            'value' => $absence?->checkin_time
                ? \Carbon\Carbon::parse($absence->checkin_time)->format('H:i')
                : '-',
            'icon' => 'phosphor-sign-in-bold',
            'description' => '',
        ],
        [
            'title' => 'Check-out',
            'value' => $absence?->checkout_time
                ? \Carbon\Carbon::parse($absence->checkout_time)->format('H:i')
                : '-',
            'icon' => 'phosphor-sign-out-bold',
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

        <!-- GPS Permission Status -->
        <div id="gpsStatus" class="mb-4 p-4 rounded hidden">
            <div id="gpsMessage"></div>
        </div>

        <div>
            <div class="text-[16px]">Selamat Datang,</div>
            <div class="font-bold text-[42px]">{{ $name }}</div>
        </div>

        <div class="text-sm text-[#555] mb-2">
            Jadwal Hari ini
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 mt-[32px] gap-16 mb-8 px-4 md:px-6 lg:px-8">
          @foreach ($stats as $stat)
    <div class="rounded-[20px] -m-6 p-[10px] bg-[#F2F2F2]">
        <div class="space-x-2 flex items-center">
            <div class="rounded-[10px] w-[36px] h-[36px] p-[10px] bg-white">
                <x-dynamic-component 
                    :component="$stat['icon']" 
                    class="h-[16px] w-[16px] text-blue-600" 
                />
            </div>
            <h2 class="text-sm text-[#555]">{{ $stat['title'] }}</h2>
        </div>

        <p class="text-xl mb-[8px] mt-[12px] font-bold text-[#555]">
            {{ $stat['value'] }}
        </p>
        
        @if($stat['description'])
            <p class="text-sm text-gray-500">
                {{ $stat['description'] }}
            </p>
        @endif
    </div>
@endforeach
        </div>

        <div class="text-sm text-[#555] mb-2">
            Status
        </div>
        <div class="grid grid-cols-2 gap-16 md:grid-cols-3 mt-[32px] px-4 md:px-6 lg:px-8">
            @foreach ($absence_status as $stat)
              <div class="rounded-[20px] -m-6 p-[10px] bg-[#F2F2F2]">
        <div class="space-x-2 flex items-center">
            <div class="rounded-[10px] w-[36px] h-[36px] p-[10px] bg-white">
                <x-dynamic-component 
                    :component="$stat['icon']" 
                    class="h-[16px] w-[16px] text-blue-600" 
                />
            </div>
            <h2 class="text-sm text-[#555]">{{ $stat['title'] }}</h2>
        </div>

        <p class="text-xl mb-[8px] mt-[12px] font-bold text-[#555]">
            {{ $stat['value'] }}
        </p>
        
        @if($stat['description'])
            <p class="text-sm text-gray-500">
                {{ $stat['description'] }}
            </p>
        @endif
    </div>
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
                {{-- Show No Schedule --}}
                <div class="relative bg-gray-500 h-[76px] rounded-[20px] overflow-hidden select-none ">
                    <div id="sliderLabel"
                        class="absolute inset-0 flex items-center justify-center text-white font-semibold pointer-events-none text-sm">
                        Tidak ada jadwal hari ini
                    </div>
                    <div class="h-full flex items-center absolute px-4">
                        <div
                            class="w-[36px] h-[36px] bg-white rounded-[10px] shadow-md cursor-pointer flex items-center justify-center transition-transform duration-100 z-10">
                            <x-dynamic-component :component="'heroicon-o-x-circle'" class="h-4 w-4 text-red-600" />
                        </div>
                    </div>
                </div>
            @elseif ($absence?->checkin_time && !$absence?->checkout_time)
                {{-- Show Checkout Slider --}}
                <div id="sliderTrack" class="relative bg-[#418CED] h-[76px] rounded-[20px] overflow-hidden select-none">
                    <div id="sliderLabel"
                        class="absolute inset-0 flex items-center justify-center text-white font-semibold pointer-events-none text-sm">
                        <span id="defaultText">Geser untuk foto dan Check-out</span>
                        <span id="locationText" class="hidden">Memeriksa lokasi...</span>
                    </div>
                    <div class="h-full flex items-center absolute px-4" id="sliderBtn">
                        <div
                            class="w-[36px] h-[36px] bg-white rounded-[10px] shadow-md cursor-pointer flex items-center justify-center transition-transform duration-100 z-10">
                            <x-dynamic-component :component="'heroicon-o-paper-airplane'" class="h-4 w-4 text-blue-600" />
                        </div>
                    </div>
                </div>
            @else
                {{-- Show Check-in Slider --}}
                <div id="sliderTrack" class="relative bg-[#418CED] h-[76px] rounded-[20px] overflow-hidden select-none">
                    <div id="sliderLabel"
                        class="absolute inset-0 flex items-center justify-center text-white font-semibold pointer-events-none text-sm">
                        <span id="defaultText">Geser untuk foto dan Check-in</span>
                        <span id="locationText" class="hidden">Memeriksa lokasi...</span>
                    </div>
                    <div class="h-full flex items-center absolute p-[20px]" id="sliderBtn">
                        <div
                            class="w-[36px] h-[36px] bg-white rounded-[10px] shadow-md cursor-pointer flex items-center justify-center transition-transform duration-100 z-10">
                           <x-phosphor-caret-double-right class="w-[16px] h-[16px] text-[#418CED]"/>

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

        .slider-disabled {
            background-color: #9CA3AF !important;
            pointer-events: none;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const isCheckin = "{{ $absence?->checkin_time && !$absence?->checkout_time ? '0' : '1' }}";
        const scheduleLocation = "{{ $schedule_today?->long_lat ?? '' }}";
        const hasSchedule = "{{ $schedule_today ? '1' : '0' }}";

        let distanceFromSchedule = null;

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        }

        function showGpsStatus(message, type = 'info') {
            const $status = $('#gpsStatus');
            const $message = $('#gpsMessage');

            $status.removeClass().addClass('border px-4 py-2 mt-2');

            switch (type) {
                case 'success': $status.addClass('bg-green-100 border-green-400 text-green-700'); break;
                case 'error': $status.addClass('bg-red-100 border-red-400 text-red-700'); break;
                case 'warning': $status.addClass('bg-yellow-100 border-yellow-400 text-yellow-700'); break;
                default: $status.addClass('bg-blue-100 border-blue-400 text-blue-700');
            }

            $message.text(message);
            $status.removeClass('hidden');
        }
function requestLocationAndSubmit() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            showGpsStatus('❌ GPS tidak didukung oleh browser ini', 'error');
            reject(new Error('Geolocation not supported'));
            return;
        }

        showGpsStatus('📍 Meminta akses lokasi...', 'info');
        $('#locationText').removeClass('hidden').addClass('pulse');
        $('#defaultText').addClass('hidden');

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                try {
                    $('#locationText').addClass('hidden').removeClass('pulse');
                    $('#defaultText').removeClass('hidden');

                    let allowed = true;

                    if (hasSchedule === '1' && scheduleLocation) {
                        const scheduleLoc = scheduleLocation.split(',');
                        if (scheduleLoc.length === 2) {
                            const scheduleLat = parseFloat(scheduleLoc[0]);
                            const scheduleLng = parseFloat(scheduleLoc[1]);
                            const userLat = position.coords.latitude;
                            const userLng = position.coords.longitude;

                            distanceFromSchedule = calculateDistance(userLat, userLng, scheduleLat, scheduleLng);
                            console.log(`Distance: ${distanceFromSchedule.toFixed(2)} meters`);

                            if (distanceFromSchedule > 100) {
                                $('#sliderLabel').text(' Kamu diluar wilayah absen');
                                allowed = false;
                                reject(new Error('Outside allowed area'));
                                return;
                            } else {
                                showGpsStatus(`✅ Lokasi OK (${distanceFromSchedule.toFixed(0)}m)`, 'success');
                            }
                        }
                    }

                    if (allowed) {
                        const lat = position.coords.latitude;
                        const long = position.coords.longitude;
                        
                        // Wait for Livewire call to complete
                        if (isCheckin === '1') {
                            await @this.call('submitCheckin', lat, long);
                        } else {
                            await @this.call('submitCheckout', lat, long);
                        }
                        resolve(); // Only resolve after everything completes
                    }
                } catch (error) {
                    reject(error);
                }
            },
            (error) => {
                $('#locationText').addClass('hidden').removeClass('pulse');
                $('#defaultText').removeClass('hidden');

                let errorMessage = 'Error mengambil lokasi';
                if (error.code === error.PERMISSION_DENIED) {
                    errorMessage = '❌ Akses ditolak';
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    errorMessage = '❌ Posisi tidak tersedia';
                } else if (error.code === error.TIMEOUT) {
                    errorMessage = '❌ Waktu habis mengambil lokasi';
                }

                $('#sliderLabel').text(errorMessage);
                showGpsStatus(errorMessage, 'error');
                resetSlider();
                reject(new Error(errorMessage));
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
}

        function resetSlider() {
            const $btn = $('#sliderBtn');
            $btn.css('transform', `translateX(0px)`);
        }

        $(function () {
            const $btn = $('#sliderBtn');
            const $track = $('#sliderTrack');

            if ($track.length === 0) return;

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

            $(document).on('mouseup touchend', async function () {
                if (!isDragging) return;
                isDragging = false;

                const currentLeft = $btn.offset().left - $track.offset().left;
                if (currentLeft >= maxDrag - 10) {
                    await requestLocationAndSubmit();

                    $btn.css('transform', `translateX(${maxDrag}px)`);

                    setTimeout(() => {
        window.location.href = '/manpower/dashboard-absence';
    }, 1000);

                } else {
                    resetSlider();
                }
            });

            Livewire.on('absenceSubmitted', () => {
                console.log('Absence submitted');
                $('#gpsStatus').addClass('hidden');
            });
        });
    </script>

</div>