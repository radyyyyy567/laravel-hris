<?php
namespace App\Http\Livewire;

use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LogoutButton extends Component
{
    public function confirmLogout()
    {
        $this->dispatchBrowserEvent('confirm-logout');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login'); // Adjust if your login route is different
    }

    public function render()
    {
        return view('livewire.logout-button');
    }
}
