<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\ScheduleAbsence;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class Schedule extends Component
{
    public function render()
    {

        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(29); // inclusive of today

        $dateRange = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateRange->push($date->toDateString());
        }

        // 2. Fetch schedule data in that range
       $schedules = ScheduleAbsence::whereBetween('absence_date', [$startDate, $endDate])
    ->whereHas('manpower.user', function ($query) {
        $query->where('nip', Auth::user()->nip);
    })
    ->with(['manpower.user'])
    ->get()
    ->groupBy(fn($item) => \Carbon\Carbon::parse($item->absence_date)->toDateString());

        // 3. Build full 30-day result, insert null or "-" if missing
        $results = $dateRange->map(function ($date) use ($schedules) {
    $item = $schedules[$date][0] ?? null;

    return (object) [
        'date' => \Carbon\Carbon::parse($date),
        'name' => $item?->manpower?->user?->name ?? '-',
        'checkin_time' => $item?->checkin_time ?? '-',
        'checkout_time' => $item?->checkout_time ?? '-',
        'status' => $item?->status ?? 'OFF',
    ];
});




        // dd($results); // Debugging line, remove in production
        return view(
            'livewire.schedule',
            ['scheduleData' => $results,
                    'type' => 'schedule'
            ]
        );
    }
}