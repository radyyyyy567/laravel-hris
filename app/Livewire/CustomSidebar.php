<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class CustomSidebar extends Component
{
    public $isOpen = false;
    public $selectedProject = null;
    public $projects = [];
    public $showAllOption = true;
    
    public function mount()
    {
        $this->loadProjects();
        $this->initializeSelectedProject();
    }
    
    private function loadProjects()
    {
        $this->projects = Project::active()->ordered()->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'label' => $project->name,
                'icon' => $project->icon,
                'url' => $project->url,
                'active' => false,
                'description' => $project->description,
                'status' => $project->status,
            ];
        })->toArray();
    }
    
    private function initializeSelectedProject()
    {
        $selectedProjectId = null;
        
        // Check URL query parameter first
        $projectParam = request()->get('project');
        if ($projectParam !== null) { // Check if parameter exists (even if empty)
            $selectedProjectId = $projectParam === '' ? null : $projectParam;
        }
        // Check session
        elseif (session()->has('selected_project_id')) {
            $selectedProjectId = session('selected_project_id');
        }
        // Check current URL pattern for project-specific routes
        else {
            foreach ($this->projects as $project) {
                if (request()->is('admin/projects/' . $project['id'] . '*')) {
                    $selectedProjectId = $project['id'];
                    break;
                }
            }
        }
        
        // Set the selected project and update active states
        if ($selectedProjectId !== null) {
            $this->setSelectedProject($selectedProjectId);
        } elseif ($projectParam === '') {
            // This is the "All" case
            $this->selectedProject = null;
            session(['selected_project_id' => null]);
        } else {
            // Default to first project if none selected
            $this->selectedProject = $this->projects[0] ?? null;
            if ($this->selectedProject) {
                $this->setSelectedProject($this->selectedProject['id']);
            }
        }
    }
    
    private function setSelectedProject($projectId)
    {
        // Find and set the selected project
        $project = collect($this->projects)->firstWhere('id', $projectId);
        
        if ($project) {
            $this->selectedProject = $project;
            
            // Update active states for all projects
            $this->projects = collect($this->projects)->map(function ($proj) use ($projectId) {
                $proj['active'] = $proj['id'] == $projectId;
                return $proj;
            })->toArray();
            
            // Store in session for persistence
            session(['selected_project_id' => $projectId]);
        }
    }
    
    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }
    
    public function selectProject($projectId = null)
    {
        if ($projectId === null) {
            // This is the "All" case
            $this->selectedProject = null;
            $this->isOpen = false;
            session(['selected_project_id' => null]);
            
            // Update active states for all projects
            $this->projects = collect($this->projects)->map(function ($proj) {
                $proj['active'] = false;
                return $proj;
            })->toArray();
        } else {
            $project = collect($this->projects)->firstWhere('id', $projectId);
            
            if ($project) {
                // Update the selected project
                $this->setSelectedProject($projectId);
            }
            $this->isOpen = false;
        }
        
        // Get current route and add project parameter
        $queryParams = request()->query();
        
        // For "All" we set empty project param, otherwise set the ID
        $queryParams['project'] = $projectId === null ? '' : $projectId;
        
        // Remove the param if it's null (clean URL)
        if ($queryParams['project'] === '') {
            unset($queryParams['project']);
        }
        
        $targetUrl = 'admin?' . http_build_query($queryParams);
        
        // Emit event to notify other components about project change
        $this->dispatch('project-changed', projectId: $projectId);
        
        // Force a full page redirect to maintain project context
        return redirect()->to($targetUrl);
    }
    
    public function getCurrentProjectId()
    {
        return $this->selectedProject['id'] ?? null;
    }
    
    public function refreshProjects()
    {
        $currentSelectedId = $this->selectedProject['id'] ?? null;
        $this->loadProjects();
        
        if ($currentSelectedId) {
            $this->setSelectedProject($currentSelectedId);
        }
    }
    
    protected function getListeners()
    {
        return [
            'refresh-projects' => 'refreshProjects',
            'select-project' => 'selectProject',
        ];
    }
    
    public function render()
    {
        $user_role = auth()->user()->getRoleNames()->first();
        
        return view('livewire.custom-sidebar', [
            'user_role' => $user_role,
            'currentProjectId' => $this->getCurrentProjectId(),
        ]);
    }
}