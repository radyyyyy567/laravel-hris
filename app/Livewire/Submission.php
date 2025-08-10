<?php
namespace App\Livewire;

use App\Models\Notification;
use App\Models\RelationNotificationUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectOvertimeUser;
use App\Models\OvertimeAssignment;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class Submission extends Component
{
    use WithFileUploads;

    public $submission_type = 'overtime';
    public $project_id;
    public $user_id;
    public $evidence;
    public $description;
    public $start_date;


    public $end_date;
    public $cuti_type = 'not_cuti';
    public $agreed_to_policy = false;
    public $overtime_entries = [];
    public $showModal = false;

    // Modal form fields
    public $modal_date;
    public $modal_start_time;
    public $modal_end_time;
    public $modal_evidence;
    public $modal_evidence_original_name;
    public $modal_description;
    public $editing_index = null;

    protected $rules = [
        'submission_type' => 'required',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'evidence' => 'nullable|file|max:10240',
        'description' => 'nullable|string',
        'cuti_type' => 'nullable|string',
        'agreed_to_policy' => 'required|accepted',
    ];

    public function mount()
    {
        $this->user_id = auth()->id();
        // Initialize with some sample overtime entries for display

    }

    public function updatedSubmissionType()
    {
        // Reset form when submission type changes
        $this->resetForm();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetModalFields();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModalFields();
        $this->editing_index = null;
    }

    public function resetModalFields()
    {
        $this->modal_date = '';
        $this->modal_start_time = '';
        $this->modal_end_time = '';
        $this->modal_evidence = '';
        $this->modal_evidence_original_name = '';
        $this->modal_description = '';
    }

    public function saveOvertimeEntry()
    {
        $this->validate([
            'modal_date' => 'required|date',
            'modal_start_time' => 'required',
            'modal_end_time' => 'required',
            'modal_evidence' => 'required',
            'modal_description' => 'required',
        ]);

        $this->modal_evidence_original_name = $this->modal_evidence
            ? $this->modal_evidence->getClientOriginalName() : null;


        $evidencePath = $this->modal_evidence
            ? $this->modal_evidence->store('temp', 'public') : null;

        // If editing and no new file uploaded → use old file path
        if ($this->editing_index !== null && $evidencePath === null) {
            Storage::disk('public')->delete($this->overtime_entries[$this->editing_index]['evidence']);
            $evidencePath = $this->overtime_entries[$this->editing_index]['evidence'] ?? null;
            $this->modal_evidence_original_name = $this->overtime_entries[$this->editing_index]['evidence_original_name'] ?? null;
        }

        $entry = [
            'date' => $this->modal_date,
            'start_time' => $this->modal_start_time,
            'end_time' => $this->modal_end_time,
            'evidence' => $evidencePath, // storage path
            'evidence_original_name' => $this->modal_evidence_original_name, // original filename from user
            'description' => $this->modal_description,
        ];

        if ($this->editing_index !== null) {
            $this->overtime_entries[$this->editing_index] = $entry;
        } else {
            $this->overtime_entries[] = $entry;
        }

        $this->closeModal();
    }

    public function editOvertimeEntry($index)
    {
        $this->editing_index = $index;
        $entry = $this->overtime_entries[$index];
        $this->modal_date = $entry['date'];
        $this->modal_start_time = $entry['start_time'];
        $this->modal_end_time = $entry['end_time'];
        $this->modal_evidence_original_name = $entry['evidence_original_name'];
        $this->modal_description = $entry['description'];
        $this->showModal = true;
    }

    public function deleteOvertimeEntry($index)
    {
        Storage::disk('public')->delete($this->overtime_entries[$index]['evidence']);
        unset($this->overtime_entries[$index]);
        $this->overtime_entries = array_values($this->overtime_entries);
    }

    public function store()
    {
        $this->validate();

        $user = User::with(['group.group', 'manpower.project.placement.placement'])->find(auth()->id());

        if ($this->submission_type === 'overtime') {
            // Handle multiple overtime entries
            foreach ($this->overtime_entries as $entry) {
                $start = Carbon::parse("{$entry['date']} {$entry['start_time']}");
                $end = Carbon::parse("{$entry['date']} {$entry['end_time']}");

                 $evidencePath = str_replace('temp/', 'evidences/', $entry['evidence']);
                 Storage::disk('public')->move($entry['evidence'], $evidencePath);
                 Storage::disk('public')->delete($entry['evidence']);

                $submission = OvertimeAssignment::create([
                    'name' => $this->submission_type,
                    'submission_date' =>  Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s')    ,
                    'start_time' => $start,
                    'end_time' => $end,
                    'description' => json_encode([
                        'submission_type' => $this->submission_type,
                        'evidence' => $evidencePath,
                        'description' => $entry['description']
                    ]),
                    'status' => 'waiting',
                ]);

                $notification = Notification::create([
                    'title' => "Pengajuan {$this->submission_type}",
                    'description' => "Pengajuan Anda sedang di review Admin harap menunggu",
                    'type' => "submission"
                ]);

                RelationNotificationUser::create([
                    'user_id' => $user->id,
                    'notification_id' => $notification->id
                ]);

                $project_overtime_user = ProjectOvertimeUser::create([
                    'project_id' => $user->manpower->first()->project->id ?? null,
                    'user_id' => $user->id,
                    'overtime_assignment_id' => $submission->id,
                ]);
            }
        } else {
            // Handle leave submission
            $start = Carbon::parse($this->start_date);
            $end = Carbon::parse($this->end_date);

            $evidencePath = $this->evidence
                ? $this->evidence->store('evidences', 'public')
                : null;

            $submission = OvertimeAssignment::create([
                'name' => $this->submission_type,
                'submission_date' => Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'),
                'start_time' => $start,
                'end_time' => $end,
                'description' => json_encode([
                            'submissiont_type' => $this->submission_type,
                            'cuti_type' => $this->cuti_type,
                            'evidence' => $evidencePath
                        ]),
                'status' => 'waiting',
            ]);

            $notification = Notification::create([
                'title' => "Pengajuan {$this->submission_type}",
                'description' => "Pengajuan Anda sedang di review Admin harap menunggu",
                'type' => "submission"
            ]);

            RelationNotificationUser::create([
                'user_id' => $user->id,
                'notification_id' => $notification->id
            ]);

            $project_overtime_user = ProjectOvertimeUser::create([
                'project_id' => $user->manpower->first()->project->id ?? null,
                'user_id' => $user->id,
                'overtime_assignment_id' => $submission->id,
            ]);
        }

        session()->flash('success', 'Pengajuan berhasil dikirim.');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'evidence',
            'description',
            'start_date',
            'end_date',
            'cuti_type',
        ]);

        $this->agreed_to_policy = false;
        $this->user_id = auth()->id();

        if ($this->submission_type === 'overtime') {
            $this->overtime_entries = [];
        }
    }

    public function render()
    {
        return view('livewire.submission', [
            'users' => User::all(),
            'projects' => Project::all(),
        ]);
    }
}