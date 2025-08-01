<?php

namespace App\Livewire;

use App\Models\OvertimeAssignment;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Submission extends Component
{
    public string $submission_name = '';
    public string $submission_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $submission_type = '';
    public string $evidence = ''; // adjust if file
    public string $project_name = '';
    public string $project_id = '';
    
    public function mount()
    {
        
        $this->project_name = Auth::user()?->manpower->first();
        $this->project_id =  $this->project_name?->project->id ?? '';
    }
    

    public function store()
    {
        $description = [
            'submission_type' => $this->submission_type,
            'evidence' => $this->evidence,
        ];

        $submission = new OvertimeAssignment();
        $submission->name = $this->submission_name;
        $submission->submission_date = $this->submission_date;
        $submission->project_name = $this->project_name;

        // Handle time vs date logic for type
        if ($this->submission_type === 'overtime') {
            $submission->hour = Carbon::parse($this->start_time)->diffInHours(Carbon::parse($this->end_time));
        } else {
            $submission->date = $this->submission_date;
        }

        $submission->description = json_encode($description);
        $submission->save();

        session()->flash('message', 'Submission saved successfully.');
    }

    public function render()
    {
        dd($this->project_id); // Debugging line, remove in production
        return view('livewire.submission');
    }
}
