<?php
namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Absence;
use App\Models\RelationAbsenceManpower;
use App\Models\ScheduleAbsence;
class DashboardAbsence extends Component
{
    public function render()
    {
        $user = Auth::user();

    if (!$user) {
        abort(403, 'Unauthorized');
    }

    $schedule_today = ScheduleAbsence::whereDate('absence_date', now())
    ->whereHas('manpower.user', function ($query) use ($user) {
        $query->where('email', $user->email);
    })
    ->with(['manpower.user'])
    ->first();

    $absence = Absence::whereDate('absence_date', now())
    ->whereHas('manpower.user', function ($query) use ($user) {
        $query->where('email', $user->email);
    })
    ->with(['manpower.user'])
    ->first();
    
    

        return view('livewire.dashboard-absence', [
            'name' => $user->name ?? 'Guest',
            'schedule_today' => $schedule_today,
            'absence' => $absence,
        ]);
    }

    public function submitCheckin($lat = null, $long = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403);
        }

        $now = now();
        
        $absence = Absence::create([
            'name' => $user->name,
            'absence_date' => $now->toDateTimeString(),
            'checkin_time' => $now->format('H:i:s'),
            'description' => 'Absen check-in otomatis via slider',
            'long_lat' => $lat && $long ? "$lat,$long" : null,
            'status' => 'present',
        ]);

        RelationAbsenceManpower::create([
            'user_id' => $user->id,
            'absence_id' => $absence->id,
        ]);

        session()->flash('message', 'Absensi berhasil disimpan dengan lokasi.');
        
        // Optionally emit an event to update the UI
        $this->dispatch('absenceSubmitted');
    }

    public function submitCheckout($lat = null, $long = null)
{
    $user = Auth::user();

    // Ambil absen hari ini
    $absence = Absence::whereDate('absence_date', now())
        ->where('name', $user->name)
        ->first();

    if (!$absence) {
        session()->flash('message', 'Data check-in tidak ditemukan.');
        return;
    }

    $absence->update([
        'checkout_time' => now()->format('H:i:s'),
    ]);

    session()->flash('message', 'Check-out berhasil.');
}
}