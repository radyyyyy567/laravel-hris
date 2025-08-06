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

    public $submission_type;
    public $submission_date;
    public $project_id;
    public $user_id;
    public $start_time;
    public $end_time;
    public $evidence;

    protected $rules = [
        'submission_type' => 'required',
        'submission_date' => 'required|date',
        'project_id' => 'required|exists:projects,id',
        'user_id' => 'required|exists:users,id',
        'start_time' => 'nullable',
        'end_time' => 'nullable',
        'evidence' => 'nullable|file|max:10240',
    ];

    public function store()
    {
        $start = $this->submission_type === 'overtime'
            ? Carbon::parse("{$this->submission_date} {$this->start_time}")
            : Carbon::parse("{$this->start_time} 00:00:00");

        $end = $this->submission_type === 'overtime'
            ? Carbon::parse("{$this->submission_date} {$this->end_time}")
            : Carbon::parse("{$this->end_time} 00:00:00");

        $evidencePath = $this->evidence
            ? $this->evidence->store('evidences', 'public')
            : null;

        $submission = OvertimeAssignment::create([
            'name' => $this->submission_type,
            'submission_date' => $this->submission_date,
            'start_time' => $start,
            'end_time' => $end,

            'description' => json_encode([
                'submission_type' => $this->submission_type,
                'evidence' => $evidencePath
            ]),
            'status' => 'waiting', // you can customize if needed
        ]);

        

        $user = User::with(['group.group', 'manpower.project.placement.placement'])->find(auth()->id());
        
        $notfication = Notification::create([
            'title' => "Pengajuan {$this->submission_type}",
            'description' => "Pengajuan Anda sedang di review Admin harap menunggu",
            'type' => "submission"
        ]);

        RelationNotificationUser::create([
            'user_id' => $user->id,
            'notification_id' => $notfication->id
        ]);

        $porject_overtime_user = ProjectOvertimeUser::create([
            'project_id' => $user->manpower->first()->project->id,
            'user_id' => $user->id,
            'overtime_assignment_id' => $submission->id,
        ]);
        dd($porject_overtime_user);

        session()->flash('success', 'Data berhasil ditambahkan.');

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'submission_type',
            'submission_date',
            'project_id',
            'user_id',
            'start_time',
            'end_time',
            'evidence',
        ]);
    }

    public function render()
    {
        return view('livewire.submission', [
            'users' => User::all(),
            'projects' => Project::all(),
        ]);
    }
}
