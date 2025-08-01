<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;


class CustomSidebar extends Component
{
    public $isOpen = false;
    public $selectedProject = null;
    public $projects = [];

    public function mount()
    {
        $this->projects = Project::active()->ordered()->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'label' => $project->name,
                'icon' => $project->icon, // Uses the getIconAttribute() method
                'url' => $project->url, // Uses the getUrlAttribute() method
                'active' => request()->is('admin/projects/' . $project->id . '*'),
                'description' => $project->description,
                'status' => $project->status,
            ];
        })->toArray();

        // Set selected project based on current URL
        $this->selectedProject = collect($this->projects)->firstWhere('active', true) 
            ?? $this->projects[0] ?? null;
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function selectProject($projectId)
    {
        $project = collect($this->projects)->firstWhere('id', $projectId);
        if ($project) {
            $this->selectedProject = $project;
            $this->isOpen = false;
            return redirect($project['url']);
        }
    }

    public function render()
    {
        return view('livewire.custom-sidebar');
    }
}