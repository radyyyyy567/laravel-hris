<?php
namespace App\Livewire;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Absence;
use App\Models\RelationAbsenceManpower;
use App\Models\ScheduleAbsence;
use Carbon\Carbon;

class DashboardAbsence extends Component
{

    public $schedule_today;
    public function render()
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $today = Carbon::now()->setTimezone(config('app.timezone'))->startOfDay();

        $this->schedule_today = ScheduleAbsence::whereDate('absence_date', $today)
            ->whereHas('manpower.user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->with(['manpower.user'])
            ->first();

        $absence = Absence::whereDate('absence_date', $today)
            ->whereHas('manpower.user', function ($query) use ($user) {
                $query->where('email', $user->email);
            })
            ->with(['manpower.user'])
            ->first();

        return view('livewire.dashboard-absence', [
            'name' => $user->name ?? 'Guest',
            'schedule_today' => $this->schedule_today,
            'absence' => $absence,
        ]);
    }

    public function submitCheckin($lat = null, $long = null)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $now = Carbon::now()->setTimezone(config('app.timezone'));
        $absence = null;

        $absence = Absence::create([
            'name' => $user->name,
            'absence_date' => $now->toDateString(),
            'checkin_time' => $now->format('H:i:s'),
            'description' => 'Absen check-in otomatis via slider',
            'long_lat' => ($lat && $long) ? "$lat,$long" : null,
            'status' => $now->format('H:i:s') > $this->schedule_today->checkin_time
                ? 'Datang Terlambat'
                : 'Tidak Clock Out',
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
    $today = Carbon::now()->setTimezone(config('app.timezone'))->startOfDay();

    // Ambil absen hari ini
    $absence = Absence::whereDate('absence_date', $today)
        ->where('name', $user->name)
        ->first();

    if (!$absence) {
        session()->flash('message', 'Data check-in tidak ditemukan.');
        return;
    }

    $now = Carbon::now()->setTimezone(config('app.timezone'));
    
    

    $absence->update([
        'checkout_time' => $now->format('H:i:s'),
        'status'        => $absence->status !== 'Datang Terlambat'
            ? 'Tepat Waktu'
            : $absence->status,
    ]);

    session()->flash('message', 'Check-out berhasil.');
}

}