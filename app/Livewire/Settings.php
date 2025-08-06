<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\User;
use Livewire\Component;

class Settings extends Component
{
    public string $email = '';
    public string $name = '';
    public string $group_id = '';
    public string $password = '';
    public string $placement = '';
    public string $project_name = '';
    public string $nip = '';

    public function mount()
    {
        $user = User::with(['group.group', 'manpower.project.placement.placement'])->find(auth()->id());

        $this->email = $user->email;
        $this->name = $user->name;
        $this->group_id = $user->group?->group?->id ?? ''; // safe access
        $this->project_name = $user->manpower[0]?->project?->name ?? '';
        $this->placement = $user->manpower[0]?->project?->placement?->placement?->name ?? '';
        $this->nip = $user->nip;
    }

    public function save()
    {
        $user = User::find(auth()->id());

        $this->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'group_id' => 'required',
        ]);

        $user->update([
            'email' => $this->email,
            'name' => $this->name,
        ]);

        // Handle group update if applicable
        $user->group()->update([
            'group_id' => $this->group_id
        ]);

        // Optional password update
        if ($this->password) {
            $user->password = bcrypt($this->password);
            $user->save();
        }

        session()->flash('message', 'Data berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.settings', [
            'groups' => Group::all(),
            'user_data' => User::with(['group.group', 'manpower.project.placement.placement'])->find(auth()->id())
        ]);
    }
}
