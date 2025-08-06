<?php

namespace App\Livewire;

use App\Models\Absence;
use Auth;
use Carbon\Carbon;
use App\Models\ScheduleAbsence;
use Illuminate\Support\Collection;
use Livewire\Component;


class HistoryAttedance extends Component
{
    public function render()
{
    $startDate = Carbon::today();
    $endDate = $startDate->copy()->addDays(29);

    $dateRange = collect();
    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
        $dateRange->push($date->toDateString());
    }

    // 1. Get ScheduleAbsence grouped by date
    $scheduleMap = ScheduleAbsence::whereBetween('absence_date', [$startDate, $endDate])
        ->whereHas('manpower.user', fn($q) => $q->where('nip', Auth::user()->nip))
        ->with(['manpower.user'])
        ->get()
        ->groupBy(fn($item) => Carbon::parse($item->absence_date)->toDateString());

    // 2. Get Absence records (actual check-in/out) grouped by date
    $absenceMap = Absence::whereBetween('absence_date', [$startDate, $endDate])
        ->whereHas('manpower.user', fn($q) => $q->where('nip', Auth::user()->nip))
        ->with(['manpower.user'])
        ->get()
        ->groupBy(fn($item) => Carbon::parse($item->absence_date)->toDateString());

    // 3. Merge both maps into a 30-day timeline
    $results = $dateRange->map(function ($date) use ($scheduleMap, $absenceMap) {
        $schedule = $scheduleMap[$date][0] ?? null;
        $absence = $absenceMap[$date][0] ?? null;

        return (object) [
            'date' => Carbon::parse($date),
            'name' => $schedule?->manpower?->user?->name ?? $absence?->manpower?->user?->name ?? '--,--',
            'status' => $schedule?->status ?? 'OFF',
            'checkin_time' => $absence?->checkin_time ?? '--,--',
            'checkout_time' => $absence?->checkout_time ?? '--,--',
        ];
    });

    return view('livewire.schedule', ['scheduleData' => $results,
'type' => "hai"]);
}

}