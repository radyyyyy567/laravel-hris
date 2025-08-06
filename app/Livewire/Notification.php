<?php
namespace App\Livewire;

use App\Models\Notification as Notifications;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notification extends Component
{
    public $notifications = [];

    public function mount()
    {
        
 $this->notifications = Notifications::query() // Use singular model name (Notification instead of Notifications)
    ->with(['user.user']) // Eager load nested relations (array syntax is cleaner)
    ->whereHas('user', function($query) { // Filter in database first for better performance
        $query->where('user_id', Auth::id());
    })
    ->latest()
    ->take(20) // Apply limit before fetching from DB
    ->get(); // apply AFTER filtering
    }

    public function render()
    {
        return view('livewire.notification');
    }
}
