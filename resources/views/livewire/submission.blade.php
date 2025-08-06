<div class="pb-[100px]">
    <div class="-mx-4 overflow-auto px-4">
        {{-- Submission Type --}}
        <div>
            <label for="submission_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Submission Type
            </label>
            <select id="submission_type" wire:model="submission_type" required
                class="w-full px-3 py-2 border border-[#418CED] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:border-[#418CED] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200">
                <option value="">Select submission type</option>
                <option value="overtime">Overtime</option>
                <option value="leave">Leave</option>
                <option value="permission">Permission</option>
                <option value="sick">Sick</option>
                <option value="cuti">Cuti</option>
            </select>
            @error('submission_type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div id="form_submission" class="">
            <form wire:submit="store" class="space-y-6">
                @csrf

                {{-- Submission Name --}}
                <div>
                    <label for="submission_name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Submission Name
                    </label>
                    <input type="text" id="submission_name" wire:model.defer="submission_name" required
                        placeholder="Enter submission name"
                        class="w-full px-3 py-2 border border-[#418CED] rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:border-[#418CED] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200">
                    @error('submission_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submission Date --}}
                <div>
                    <label for="submission_date"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span id="date_label">Submission Date</span>
                    </label>
                    <input type="date" id="submission_date" wire:model.defer="submission_date" required
                        class="w-full px-3 py-2 border border-[#418CED] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:border-[#418CED] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200">
                    @error('submission_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Start and End Fields Container --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_field"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span id="start_label">Start Date</span>
                        </label>
                        <input type="date" id="start_field" wire:model.defer="start_time"
                            class="w-full px-3 py-2 border border-[#418CED] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:border-[#418CED] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_field" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span id="end_label">End Date</span>
                        </label>
                        <input type="date" id="end_field" wire:model.defer="end_time"
                            class="w-full px-3 py-2 border border-[#418CED] rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:border-[#418CED] dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors duration-200">
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Evidence Photo --}}
                <div>
                    <label for="evidence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Evidence Photo
                    </label>
                    <div
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md dark:border-gray-600 hover:border-[#418CED] dark:hover:border-[#418CED] transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label for="evidence"
                                    class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-[#418CED] hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#418CED]">
                                    <span>Upload a photo</span>
                                    <input id="evidence" name="evidence" type="file" wire:model="evidence"
                                        accept="image/*" class="sr-only">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                PNG, JPG, GIF up to 10MB
                            </p>
                        </div>
                    </div>
                    @if ($evidence)
                        <div class="mt-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Selected:
                                {{ $evidence->getClientOriginalName() }}</p>
                        </div>
                    @endif
                    @error('evidence')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>


                {{-- Submit Button --}}
                <button type="submit"
                    class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-[#418CED] rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-[#418CED] focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Submit</span>
                    <span wire:loading>Submitting...</span>
                </button>

                {{-- Flash Message --}}
                @if (session()->has('message'))
                    <div
                        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded dark:bg-green-800 dark:border-green-600 dark:text-green-200">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div
                        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded dark:bg-red-800 dark:border-red-600 dark:text-red-200">
                        {{ session('error') }}
                    </div>
                @endif
            </form>
        </div>

    </div>
</div>
<script>
    function updateInputTypes() {
        const submissionTypeSelect = document.getElementById('submission_type');
        const dateLabel = document.getElementById('date_label');
        const startLabel = document.getElementById('start_label');
        const endLabel = document.getElementById('end_label');
        const startField = document.getElementById('start_field');
        const endField = document.getElementById('end_field');

        if (!submissionTypeSelect || !startField || !endField) return;

        const selectedType = submissionTypeSelect.value;

        switch (selectedType) {
            case 'overtime':
            case 'permission':
            case 'work_outside':
                dateLabel.textContent = selectedType.charAt(0).toUpperCase() + selectedType.slice(1).replace('_', ' ') + ' Date';
                startLabel.textContent = 'Start Time';
                endLabel.textContent = 'End Time';
                startField.type = 'time';
                endField.type = 'time';
                break;

            case 'leave':
            case 'sick':
                dateLabel.textContent = selectedType.charAt(0).toUpperCase() + selectedType.slice(1) + ' Date';
                startLabel.textContent = 'Start Date';
                endLabel.textContent = 'End Date';
                startField.type = 'date';
                endField.type = 'date';
                break;

            default:
                dateLabel.textContent = 'Submission Date';
                startLabel.textContent = 'Start Date';
                endLabel.textContent = 'End Date';
                startField.type = 'date';
                endField.type = 'date';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateInputTypes();

        const submissionTypeSelect = document.getElementById('submission_type');
        if (submissionTypeSelect) {
            submissionTypeSelect.addEventListener('change', updateInputTypes);
        }

        // Hook into Livewire render
        Livewire.hook('message.processed', (message, component) => {
            updateInputTypes();
        });
    });
</script>

