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
        $this->loadProjects();
        $this->initializeSelectedProject();
    }
    
    private function loadProjects()
    {
        $this->projects = Project::active()->ordered()->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'label' => $project->name,
                'icon' => $project->icon, // Uses the getIconAttribute() method
                'url' => $project->url, // Uses the getUrlAttribute() method
                'active' => false, // We'll set this separately
                'description' => $project->description,
                'status' => $project->status,
            ];
        })->toArray();
    }
    
    private function initializeSelectedProject()
    {
        // Priority order for determining selected project:
        // 1. URL query parameter 'project'
        // 2. Session stored project
        // 3. Current route project (from URL pattern)
        // 4. First available project
        
        $selectedProjectId = null;
        
        // Check URL query parameter first
        if (request()->get('project')) {
            $selectedProjectId = request()->get('project');
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
        if ($selectedProjectId) {
            $this->setSelectedProject($selectedProjectId);
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
    
    public function selectProject($projectId)
    {
        $project = collect($this->projects)->firstWhere('id', $projectId);
        
        if ($project) {
            // Update the selected project
            $this->setSelectedProject($projectId);
            $this->isOpen = false;
            
            // Get current route and add project parameter
            $currentRoute = request()->route()->getName();
            $currentUrl = url()->current();
            
            // Build the redirect URL with project parameter
            $queryParams = request()->query();
            $queryParams['project'] = $projectId;
            
            $targetUrl = '/admin?' . http_build_query($queryParams);
            
            // Emit event to notify other components about project change
            $this->dispatch('project-changed', projectId: $projectId);
            
            // Force a full page redirect to maintain project context
            return redirect()->to($targetUrl);
        }
    }
    
    // Method to get current project ID for use in navigation URLs
    public function getCurrentProjectId()
    {
        return $this->selectedProject['id'] ?? null;
    }
    
    // Method to refresh projects if needed
    public function refreshProjects()
    {
        $currentSelectedId = $this->selectedProject['id'] ?? null;
        $this->loadProjects();
        
        if ($currentSelectedId) {
            $this->setSelectedProject($currentSelectedId);
        }
    }
    
    // Handle project updates from other components
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