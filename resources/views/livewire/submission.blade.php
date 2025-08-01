<div>
    <form wire:submit.prevent="store" class="space-y-4">

        <!-- Submission Name -->
        <div>
            <label for="submission_name" class="block font-medium text-sm text-gray-700">Submission Name</label>
            <input type="text" id="submission_name" wire:model.defer="submission_name" class="mt-1 block w-full border rounded p-2" />
        </div>

        <!-- Submission Date -->
        <div>
            <label for="submission_date" class="block font-medium text-sm text-gray-700">Submission Date</label>
            <input type="date" id="submission_date" wire:model.defer="submission_date" class="mt-1 block w-full border rounded p-2" />
        </div>

        <!-- Submission Type -->
        <div>
            <label for="submission_type" class="block font-medium text-sm text-gray-700">Submission Type</label>
            <select id="submission_type" wire:model="submission_type" class="mt-1 block w-full border rounded p-2">
                <option value="">-- Select Type --</option>
                <option value="overtime">Overtime</option>
                <option value="leave">Leave</option>
                <option value="permission">Permission</option>
                <!-- Add more types as needed -->
            </select>
        </div>

        <!-- Time Fields (only show if overtime) -->
        @if($submission_type === 'overtime')
            <div>
                <label for="start_time" class="block font-medium text-sm text-gray-700">Start Time</label>
                <input type="time" id="start_time" wire:model.defer="start_time" class="mt-1 block w-full border rounded p-2" />
            </div>

            <div>
                <label for="end_time" class="block font-medium text-sm text-gray-700">End Time</label>
                <input type="time" id="end_time" wire:model.defer="end_time" class="mt-1 block w-full border rounded p-2" />
            </div>
        @endif

        <!-- Evidence -->
        <div>
            <label for="evidence" class="block font-medium text-sm text-gray-700">Evidence (URL or description)</label>
            <input type="text" id="evidence" wire:model.defer="evidence" class="mt-1 block w-full border rounded p-2" />
        </div>

        <!-- Project (autofilled, not editable) -->
        <div>
            <label class="block font-medium text-sm text-gray-700">Project</label>
            <input type="text" value="{{ $project_name }}" disabled class="mt-1 block w-full bg-gray-100 border rounded p-2" />
        </div>

        <!-- Submit -->
        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Submit
            </button>
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
            <div class="text-green-600 mt-2">
                {{ session('message') }}
            </div>
        @endif
    </form>
</div>
