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
    public $isSpv = false;
    
    public function mount()
    {
        // Check if user has SPV role
        $this->isSpv = auth()->user()->hasRole('spv');
        
        $this->loadProjects();
        $this->initializeSelectedProject();
        
        // Ensure global variable is set after initialization
        if ($this->selectedProject) {
            $GLOBALS['project_global'] = $this->selectedProject['id'];
        } else {
            $GLOBALS['project_global'] = '';
        }
    }
    
    private function loadProjects()
    {
        // If user is SPV, only load their assigned project
        
        if ($this->isSpv) {
            $userPicProject = auth()->user()->pic()->first();
            
            if ($userPicProject && $userPicProject->project->id) {
                $project = Project::find($userPicProject->project_id);
                
                if ($project) {
                    $this->projects = [[
                        'id' => $project->id,
                        'label' => $project->name,
                        'icon' => $project->icon,
                        'url' => $project->url,
                        'active' => true,
                        'description' => $project->description,
                        'status' => $project->status,
                    ]];
                    
                    // Hide "All" option for SPV
                    $this->showAllOption = false;
                } else {
                    $this->projects = [];
                }
            } else {
                $this->projects = [];
            }
        } else {
            // For non-SPV users, load all active projects
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
    }
    
    private function initializeSelectedProject()
    {
        // If user is SPV, force select their PIC project
        if ($this->isSpv) {
            $userPicProject = auth()->user()->pic()->first();
            
            if ($userPicProject && $userPicProject->project_id && !empty($this->projects)) {
                $projectId = $userPicProject->project_id;
                $this->setSelectedProject($projectId);
                return;
            } else {
                // SPV has no assigned project
                $this->selectedProject = null;
                $GLOBALS['project_global'] = '';
                session(['selected_project_id' => null]);
                return;
            }
        }
        
        // For non-SPV users, use the original logic
        $selectedProjectId = null;
        
        // Check global variable first
        if (isset($GLOBALS['project_global'])) {
            $selectedProjectId = $GLOBALS['project_global'] === '' ? null : $GLOBALS['project_global'];
        }
        // Check session as fallback
        elseif (session()->has('selected_project_id')) {
            $selectedProjectId = session('selected_project_id');
        }
        // Check current URL pattern for project-specific routes (fallback)
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
        } elseif (isset($GLOBALS['project_global']) && $GLOBALS['project_global'] === '') {
            // This is the "All" case
            $this->selectedProject = null;
            session(['selected_project_id' => null]);
            $GLOBALS['project_global'] = '';
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
            
            // Set global variable
            $GLOBALS['project_global'] = $projectId;
        }
    }
    
    public function toggleDropdown()
    {
        // Don't allow dropdown toggle for SPV if they only have one project
        if ($this->isSpv && count($this->projects) <= 1) {
            return;
        }
        
        $this->isOpen = !$this->isOpen;
    }
    
    public function selectProject($projectId = null)
    {
        // Don't allow project selection for SPV users
        if ($this->isSpv) {
            $this->isOpen = false;
            return;
        }
        
        if ($projectId === null) {
            // This is the "All" case
            $this->selectedProject = null;
            $this->isOpen = false;
            session(['selected_project_id' => null]);
            $GLOBALS['project_global'] = '';
            
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
        
        // Emit event to notify other components about project change
        $this->dispatch('project-changed', projectId: $projectId);
        
        // No need to redirect anymore, just refresh the current page if needed
        // The global variable will persist the selection
        $this->dispatch('$refresh');

        return redirect()->to(request()->header('Referer'));
    }
    
    public function getCurrentProjectId()
    {
        return $this->selectedProject['id'] ?? null;
    }
    
    public function refreshProjects()
    {
        $currentSelectedId = $this->selectedProject['id'] ?? null;
        $this->loadProjects();
        
        if ($currentSelectedId && !$this->isSpv) {
            $this->setSelectedProject($currentSelectedId);
        } elseif ($this->isSpv) {
            // Force reinitialize for SPV
            $this->initializeSelectedProject();
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
            'isSpv' => $this->isSpv,
            'canSelectProject' => !$this->isSpv, // Pass this to view for UI control
        ]);
    }
}