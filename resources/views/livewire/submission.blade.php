<!-- resources/views/livewire/submission.blade.php -->
<div class="min-h-screen bg-gradient-to-br">

    <!-- Main Content -->
    <div class="bg-white rounded-t-3xl pb-24" id="mainContent">
        <div class="text-[16px] text-[#555] mb-[10px]">Buat Pengajuan</div>

        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg animate-fade-in">
                {{ session('success') }}
            </div>
        @endif

        <!-- Submission Type -->
        <div class="mb-6 form-group mt-[10px]">
            <label class="block text-sm font-medium text-[#555] mb-2">Jenis Pengajuan</label>
            <select wire:model.live="submission_type"
                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]">
                <option value="overtime">Overtime</option>
                <option value="cuti">Cuti</option>
            </select>
            @error('submission_type')
                <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
            @enderror
        </div>

        @if($submission_type === 'overtime')
            <!-- Overtime List View -->
            <div class="space-y-4 mb-6" id="overtimeEntriesContainer">
                @foreach($overtime_entries as $index => $entry)
                    <div class="border border-gray-200 rounded-lg p-4 overtime-entry animate-slide-in" data-index="{{ $index }}"
                        style="animation-delay: {{ $index * 0.1 }}s">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center text-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm font-medium">
                                    {{ date('d M Y', strtotime($entry['date'])) }} - Input absen..
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Start</label>
                                <div class="text-2xl font-bold text-gray-800 time-display">
                                    {{ $entry['start_time'] }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">End</label>
                                <div class="text-2xl font-bold text-gray-800 time-display">
                                    {{ $entry['end_time'] }}
                                </div>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" onclick="editOvertimeEntry({{ $index }})"
                                class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center justify-center transition-all duration-200 hover:scale-105 btn-ripple">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit
                            </button>
                            <button type="button" onclick="deleteOvertimeEntry({{ $index }})"
                                class="flex-1 py-2 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center justify-center transition-all duration-200 hover:scale-105 btn-ripple">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach

                <!-- Add Overtime Button -->
                <button type="button" onclick="openOvertimeModal()"
                    class="w-full py-3 px-4 border-2 border-dashed border-blue-300 rounded-lg text-blue-600 hover:bg-blue-50 flex items-center justify-center transition-all duration-300 hover:border-blue-400 add-overtime-btn">
                    <svg class="w-5 h-5 mr-2 transition-transform duration-200" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Overtime Lain
                </button>
            </div>

        @else
            <!-- Leave Form -->
            <form wire:submit.prevent="store" class="space-y-6" id="leaveForm">
                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Start Date -->

                    <div class="form-group">
                        <label class="block text-sm font-medium text-[#555] mb-2">Tanggal Mulai</label>
                        <div class="relative">
                            <input type="date" wire:model="start_date" id="start_date"
                                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none text-sm rounded-[5px] text-[#555] focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none date-input"
                                max="{{ now()->addYear()->format('Y-m-d') }}" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-[10px] pointer-events-none">
                                <div class="h-[36px] w-[36px] p-[10px] rounded-[10px] bg-white">
                                    <x-phosphor-calendar-blank-bold class="h-4 w-4 text-[#418CED]" />
                                </div>
                            </div>
                        </div>
                        @error('start_date')
                            <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="form-group">
                        <label class="block text-sm font-medium text-[#555] mb-2">Tanggal Selesai</label>
                        <div class="relative">
                            <input type="date" wire:model="end_date" id="end_date"
                                class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none text-sm rounded-[5px] text-[#555] focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none date-input"
                                max="{{ now()->addYear()->format('Y-m-d') }}" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-[10px] pointer-events-none">
                                <div class="h-[36px] w-[36px] p-[10px] rounded-[10px] bg-white">
                                    <x-phosphor-calendar-blank-bold class="h-4 w-4 text-[#418CED]" />
                                </div>
                            </div>
                        </div>
                        @error('end_date')
                            <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Evidence Upload -->
                <div class="form-group">
                    <label class="block text-sm font-medium text-[#555] mb-2">Evidence</label>
                    <label class="relative w-full cursor-pointer">
                        <div
                            class="relative px-[15px] py-[17px] w-full bg-[#F2F2F2] border-none rounded-[5px] flex items-center justify-between hover:bg-[#EBEBEB] transition-colors">
                            <span class="text-sm text-[#555]" id="file-label">
                                @if($evidence)
                                    {{ $evidence->getClientOriginalName() }}
                                @else
                                    Pilih file
                                @endif
                            </span>
                            <div class="absolute h-[36px] w-[36px] p-[10px] rounded-[10px] bg-white right-[10px]">
                                <x-phosphor-folder-open-bold class="h-4 w-4 text-[#418CED]" />
                            </div>
                        </div>
                        <input type="file" wire:model="evidence" class="absolute inset-0 opacity-0 cursor-pointer"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                    </label>
                    @error('evidence')
                        <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-6 form-group mt-[10px]">
                    <label class="block text-sm font-medium text-[#555] mb-2">Jenis Pengajuan</label>
                    <select wire:model.live="cuti_type"
                        class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none placeholder:text-sm rounded-[5px] placeholder:text-[#D0D0D0]">
                        <option value="tahunan">Cuti Tahunan</option>
                        <option value="nasional">Cuti Nasional</option>
                    </select>
                    @error('cuti_type')
                        <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Policy Agreement -->
                <div class="flex items-start space-x-2 form-group">
                    <input type="checkbox" wire:model="agreed_to_policy" id="policy"
                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all duration-200">
                    <label for="policy" class="text-sm text-gray-700">
                        Saya sudah menyetujui <a href="#"
                            class="text-blue-600 underline hover:text-blue-700 transition-colors">kebijakan</a>.
                    </label>
                </div>
                @error('agreed_to_policy')
                    <span class="text-red-500 text-sm block mt-1 animate-shake">{{ $message }}</span>
                @enderror

                <!-- Submit Button -->
                <button type="submit" id="submitLeaveBtn"
                    class="w-full py-3 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium hover:scale-105 btn-ripple submit-btn">
                    <span class="btn-text">Kirim</span>
                    <span class="hidden btn-loading flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Mengirim...
                    </span>
                </button>
            </form>
        @endif

        @if($submission_type === 'overtime' && count($overtime_entries) > 0)
            <!-- Overtime Submit Section -->
            <form wire:submit.prevent="store" class="space-y-6" id="overtimeForm">
                <!-- Policy Agreement -->
                <div class="flex items-start space-x-2 form-group">
                    <input type="checkbox" wire:model="agreed_to_policy" id="policy_overtime"
                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all duration-200">
                    <label for="policy_overtime" class="text-sm text-gray-700">
                        Saya sudah menyetujui <a href="#"
                            class="text-blue-600 underline hover:text-blue-700 transition-colors">kebijakan</a>.
                    </label>
                </div>
                @error('agreed_to_policy')
                    <span class="text-red-500 text-sm block mt-1 animate-shake">{{ $message }}</span>
                @enderror

                <!-- Submit Button -->
                <button type="submit" id="submitOvertimeBtn"
                    class="w-full py-3 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium hover:scale-105 btn-ripple submit-btn">
                    <span class="btn-text">Kirim</span>
                    <span class="btn-loading  flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Mengirim...
                    </span>
                </button>
            </form>
        @endif
    </div>

    <!-- Bottom Navigation -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-2 nav-bottom">
        <div class="flex justify-around">
            <button
                class="flex flex-col items-center p-2 text-gray-400 nav-item transition-colors duration-200 hover:text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </button>
            <button
                class="flex flex-col items-center p-2 text-gray-400 nav-item transition-colors duration-200 hover:text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
            <button
                class="flex flex-col items-center p-2 text-gray-400 nav-item transition-colors duration-200 hover:text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </button>
            <button class="flex flex-col items-center p-2 text-blue-600 border-b-2 border-blue-600 nav-item">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </button>
            <button
                class="flex flex-col items-center p-2 text-gray-400 nav-item transition-colors duration-200 hover:text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Enhanced Modal Overlay -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end modal-backdrop animate-fade-in mb-[80px]"
            onclick="closeModalOnBackdrop(event)" id="modalOverlay">
            <!-- Modal Content -->
            <div class="bg-white w-full rounded-t-2xl p-6 transform transition-transform duration-300 ease-out modal-content animate-slide-up"
                onclick="event.stopPropagation()">

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $editing_index !== null ? 'Edit Overtime' : 'Tambah Overtime' }}
                    </h3>
                    <button onclick="closeOvertimeModal()"
                        class="text-gray-500 hover:text-gray-700 transition-colors duration-200 p-1 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveOvertimeEntry" class="space-y-6" id="overtimeModalForm">
                    <!-- Description -->
                    <div class="form-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea wire:model="description" placeholder="Masukan deskripsi overtime" rows="3"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 transition-all duration-300 resize-none"></textarea>
                    </div>





                    <!-- Time Range -->
                    <div class="grid grid-cols-2 gap-4">
                        
                        <!-- Date -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-[#555] mb-2">Tanggal Pengajuan</label>
                            <div class="relative">
                                <input type="date" wire:model="modal_date" id="modal_date"
                                    class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none text-sm rounded-[5px] text-[#555] focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none date-input"
                                    max="{{ now()->addYear()->format('Y-m-d') }}" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-[10px] pointer-events-none">
                                    <div class="h-[36px] w-[36px] p-[10px] rounded-[10px] bg-white">
                                        <x-phosphor-calendar-blank-bold class="h-4 w-4 text-[#418CED]" />
                                    </div>
                                </div>
                            </div>
                            @error('modal_date')
                                <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Evidence -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-[#555] mb-2">Evidence</label>
                            <label class="relative w-full cursor-pointer">
                                <div
                                    class="relative px-[15px] py-[17px] w-full bg-[#F2F2F2] border-none rounded-[5px] flex items-center justify-between hover:bg-[#EBEBEB] transition-colors">
                                    <span class="text-sm text-[#555]" id="file-label">
                                        @if($modal_evidence)
                                            {{ $modal_evidence->getClientOriginalName() }}
                                        @elseif($modal_evidence_original_name)
                                            {{ $modal_evidence_original_name}}
                                        @else
                                            Pilih file
                                        @endif
                                    </span>
                                    <div class="absolute h-[36px] w-[36px] p-[10px] rounded-[10px] bg-white right-[10px]">
                                        <x-phosphor-folder-open-bold class="h-4 w-4 text-[#418CED]" />
                                    </div>
                                </div>
                                <input type="file" wire:model="modal_evidence" class="absolute inset-0 opacity-0 cursor-pointer"
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                            </label>
                            @error('evidence')
                                <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Date -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-[#555] mb-2">Jam Mulai</label>
                            <div class="relative">
                                <input type="time" wire:model="modal_start_time" id="modal_start_time"
                                    class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none text-sm rounded-[5px] text-[#555] focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none date-input"
                                    max="{{ now()->addYear()->format('Y-m-d') }}" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-[10px] pointer-events-none">
                                    <div class="h-[36px] w-[36px] p-[10px] rounded-[10px] bg-white">
                                        <x-phosphor-calendar-blank-bold class="h-4 w-4 text-[#418CED]" />
                                    </div>
                                </div>
                            </div>
                            @error('modal_start_time')
                                <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Date -->
                        <div class="form-group">
                            <label class="block text-sm font-medium text-[#555] mb-2">Jam Selesai</label>
                            <div class="relative">
                                <input type="time" wire:model="modal_end_time" id="modal_end_time"
                                    class="px-[15px] py-[15px] w-full bg-[#F2F2F2] border-none text-sm rounded-[5px] text-[#555] focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none date-input"
                                    max="{{ now()->addYear()->format('Y-m-d') }}" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-[10px] pointer-events-none">
                                    <div class="h-[36px] w-[36px] p-[10px] rounded-[10px] bg-white">
                                        <x-phosphor-calendar-blank-bold class="h-4 w-4 text-[#418CED]" />
                                    </div>
                                </div>
                            </div>
                            @error('modal_end_time')
                                <span class="text-red-500 text-sm animate-shake">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Modal Actions -->
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeOvertimeModal()"
                            class="flex-1 py-3 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-all duration-200 hover:scale-105">
                            Batal
                        </button>
                        <button type="submit" id="saveOvertimeBtn"
                            class="flex-1 py-3 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-all duration-200 hover:scale-105 btn-ripple submit-btn">
                            <span class="btn-text">Simpan</span>
                            <span  class="btn-loading flex   items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        /* Hide default date picker icons and style date inputs */
        .date-input::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            right: 0;
            width: 50px;
            height: 100%;
            cursor: pointer;
        }

        .date-input::-webkit-datetime-edit-text {
            color: #555;
        }

        .date-input::-webkit-datetime-edit-month-field,
        .date-input::-webkit-datetime-edit-day-field,
        .date-input::-webkit-datetime-edit-year-field {
            color: #555;
        }

        .date-input:invalid {
            color: #AFAFAF;
        }

        /* Enhanced Animations */
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Animation Classes */
        .animate-slide-up {
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-slide-in {
            animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }

        .animate-pulse-custom {
            animation: pulse 1s infinite;
        }

        /* Enhanced Form Styling */
        .form-group {
            position: relative;
            transition: all 0.3s ease;
        }

        /* 
                    .form-group input:focus,
                    .form-group select:focus,
                    .form-group textarea:focus {
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                        transform: scale(1.02);
                    } */

        /* Button Enhancements */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }

        .btn-ripple::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }

        .btn-ripple:active::before {
            width: 300px;
            height: 300px;
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Modal Enhancements */
        .modal-backdrop {
            backdrop-filter: blur(4px);
        }

        .modal-content {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Overtime Entry Animations */
        .overtime-entry {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .overtime-entry:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .overtime-entry.removing {
            animation: slideOut 0.3s ease-in forwards;
        }

        @keyframes slideOut {
            to {
                transform: translateX(-100%);
                opacity: 0;
                height: 0;
                margin: 0;
                padding: 0;
            }
        }

        /* Time Display Enhancement */
        .time-display {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* File Upload Enhancement */
        .file-upload-area:hover .upload-icon {
            transform: scale(1.1) translateY(-2px);
        }

        .file-upload-area:hover {
            background: linear-gradient(135deg, #ebf8ff 0%, #e0f2fe 100%);
        }

        /* Navigation Enhancement */
        .nav-item:hover {
            transform: translateY(-2px);
        }

        .nav-bottom {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.95);
        }

        /* Add Button Enhancement */
        .add-overtime-btn:hover svg {
            transform: rotate(90deg);
        }

        /* Custom scrollbar */
        .modal-content::-webkit-scrollbar {
            width: 6px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }


        /* Enhanced focus states */
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Enhanced JavaScript for Livewire Integration
        class SubmissionEnhancer {
            constructor() {
                this.isSubmitting = false;
                this.init();
            }

            init() {
                this.bindEvents();
                this.setupLivewireHooks();
                this.setupAnimations();
            }

            bindEvents() {
                this.setupFormSubmissions();
                this.setupFileUpload();
                this.setupKeyboardShortcuts();
                this.setupButtonEnhancements();
            }

            setupFormSubmissions() {
                const forms = ['#leaveForm', '#overtimeForm', '#overtimeModalForm'];

                forms.forEach(formSelector => {
                    const form = document.querySelector(formSelector);
                    if (form) {
                        form.addEventListener('submit', (e) => {
                            this.handleFormSubmit(e);
                        });
                    }
                });
            }

            handleFormSubmit(event) {
                if (this.isSubmitting) return;

                this.isSubmitting = true;
                const submitBtn = event.target.querySelector('.submit-btn');

                if (submitBtn) {
                    this.showButtonLoading(submitBtn);
                }

                setTimeout(() => {
                    this.isSubmitting = false;
                    if (submitBtn) {
                        this.hideButtonLoading(submitBtn);
                    }
                }, 2000);
            }

            showButtonLoading(button) {
                const textSpan = button.querySelector('.btn-text');
                const loadingSpan = button.querySelector('.btn-loading');

                if (textSpan) textSpan.classList.add('hidden');
                if (loadingSpan) loadingSpan.classList.remove('hidden');

                button.disabled = true;
                button.classList.add('opacity-75');
            }

            hideButtonLoading(button) {
                const textSpan = button.querySelector('.btn-text');
                const loadingSpan = button.querySelector('.btn-loading');

                if (textSpan) textSpan.classList.remove('hidden');
                if (loadingSpan) loadingSpan.classList.add('hidden');

                button.disabled = false;
                button.classList.remove('opacity-75');
            }

            setupFileUpload() {
                const fileInput = document.getElementById('evidence_modal');
                if (fileInput) {
                    fileInput.addEventListener('change', (e) => {
                        this.handleFileSelection(e);
                    });
                }
            }

            handleFileSelection(event) {
                const file = event.target.files[0];
                const uploadArea = document.querySelector('.file-upload-area');

                if (file) {
                    uploadArea.classList.add('border-green-300', 'bg-green-50');
                    uploadArea.classList.remove('border-gray-300');

                    uploadArea.classList.add('animate-pulse-custom');
                    setTimeout(() => {
                        uploadArea.classList.remove('animate-pulse-custom');
                    }, 1000);
                }
            }

            setupKeyboardShortcuts() {
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.closeModal();
                    }

                    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                        const activeForm = document.querySelector('form:focus-within');
                        if (activeForm) {
                            activeForm.dispatchEvent(new Event('submit', { cancelable: true }));
                        }
                    }
                });
            }

            setupButtonEnhancements() {
                document.querySelectorAll('.btn-ripple').forEach(button => {
                    button.addEventListener('click', (e) => {
                        this.createRipple(e);
                    });
                });

                document.querySelectorAll('button').forEach(button => {
                    button.addEventListener('mouseenter', function () {
                        if (!this.disabled) {
                            this.style.transform = 'translateY(-2px)';
                            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
                        }
                    });

                    button.addEventListener('mouseleave', function () {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    });
                });
            }

            createRipple(event) {
                const button = event.currentTarget;
                const ripple = document.createElement('span');

                const rect = button.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = event.clientX - rect.left - size / 2;
                const y = event.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                                position: absolute;
                                width: ${size}px;
                                height: ${size}px;
                                background: rgba(255, 255, 255, 0.6);
                                border-radius: 50%;
                                left: ${x}px;
                                top: ${y}px;
                                animation: ripple 0.6s linear;
                                pointer-events: none;
                                z-index: 1;
                            `;

                button.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }

            setupLivewireHooks() {
                document.addEventListener('livewire:load', () => {
                    this.onLivewireLoad();
                });

                document.addEventListener('livewire:update', () => {
                    this.onLivewireUpdate();
                });
            }

            onLivewireLoad() {
                console.log('Livewire loaded - Enhanced UI ready');
                this.addPageTransition();
            }

            onLivewireUpdate() {
                this.setupAnimations();
                this.bindNewElements();
            }

            bindNewElements() {
                const newButtons = document.querySelectorAll('.btn-ripple:not([data-enhanced])');
                newButtons.forEach(button => {
                    button.setAttribute('data-enhanced', 'true');
                    button.addEventListener('click', (e) => {
                        this.createRipple(e);
                    });
                });
            }

            setupAnimations() {
                const overtimeEntries = document.querySelectorAll('.overtime-entry');
                overtimeEntries.forEach((entry, index) => {
                    entry.style.animationDelay = `${index * 0.1}s`;
                    entry.classList.add('animate-slide-in');
                });

                const formGroups = document.querySelectorAll('.form-group');
                formGroups.forEach((group, index) => {
                    group.style.animationDelay = `${index * 0.05}s`;
                    group.classList.add('animate-fade-in');
                });
            }

            addPageTransition() {
                const mainContent = document.getElementById('mainContent');
                if (mainContent) {
                    mainContent.classList.add('animate-slide-up');
                }
            }

            closeModal() {
                if (typeof closeOvertimeModal === 'function') {
                    closeOvertimeModal();
                }
            }
        }

        // Global functions for Livewire integration
        function openOvertimeModal() {
            @this.call('openModal');
        }

        function closeOvertimeModal() {
            @this.call('closeModal');
        }

        function editOvertimeEntry(index) {
            @this.call('editOvertimeEntry', index);
        }

        function deleteOvertimeEntry(index) {
            if (confirm('Apakah Anda yakin ingin menghapus overtime entry ini?')) {
                const entry = document.querySelector(`[data-index="${index}"]`);
                if (entry) {
                    entry.classList.add('removing');
                    setTimeout(() => {
                        @this.call('deleteOvertimeEntry', index);
                    }, 300);
                } else {
                    @this.call('deleteOvertimeEntry', index);
                }
            }
        }

        function closeModalOnBackdrop(event) {
            if (event.target === event.currentTarget) {
                closeOvertimeModal();
            }
        }

        function handleFileUpload(input) {
            const file = input.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) { // 10MB
                    alert('File terlalu besar. Maksimal 10MB.');
                    input.value = '';
                    return;
                }

                const uploadArea = input.closest('.file-upload-area');
                if (uploadArea) {
                    uploadArea.classList.add('border-green-300', 'bg-green-50');
                    uploadArea.classList.remove('border-gray-300');
                }
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            window.submissionEnhancer = new SubmissionEnhancer();

            // Add smooth scroll behavior
            document.documentElement.style.scrollBehavior = 'smooth';

            // Add page visibility handling
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') {
                    window.submissionEnhancer.setupAnimations();
                }
            });
        });

        // Add resize handler for responsive behavior
        window.addEventListener('resize', function () {
            const modal = document.querySelector('.modal-content');
            if (modal) {
                modal.style.maxHeight = '90vh';
            }
        });

        // Add touch support for mobile
        if ('ontouchstart' in window) {
            document.documentElement.classList.add('touch-device');

            const style = document.createElement('style');
            style.textContent = `
                            .touch-device button:hover {
                                transform: none;
                                box-shadow: none;
                            }
                            .touch-device .overtime-entry:hover {
                                transform: none;
                                box-shadow: none;
                            }
                        `;
            document.head.appendChild(style);
        }
    </script>
@endpush