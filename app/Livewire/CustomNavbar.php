<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class CustomNavbar extends Component
{
    public function render()
    {
        
        return view('livewire.custom-navbar', [
            'user_data' => User::with(['group.group', 'manpower.project.placement.placement'])->find(auth()->id())
        ]);
    }
}