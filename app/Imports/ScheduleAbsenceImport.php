<?php

namespace App\Imports;

use App\Models\ScheduleAbsence;
use App\Models\User;
use App\Models\RelationScheduleManpower;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ScheduleAbsenceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Step 1: Find the manpower by NIP
        $manpower = User::where('nip', $row['nip'])->first();

        // Optional: Fail if NIP not found
        if (! $manpower) {
            return null; // or throw ValidationException if preferred
        }

        $user = Auth::user()->name; // Get the currently authenticated user

        // Step 2: Create the schedule absence
        $scheduleAbsence = new ScheduleAbsence([
            'name' => $user,
            'absence_date' => $row['absence_date'],
            'checkin_time' => $row['checkin_time'],
            'checkout_time' => $row['checkout_time'],
            'description' => $row['description'],
            'radius' => $row['radius'],
            'long_lat' => $row['long_lat'],
            'status' => $row['status']
        ]);

        $scheduleAbsence->save();

        // Step 3: Create relation (optional)
        RelationScheduleManpower::create([
            'schedule_id' => $scheduleAbsence->id,
            'user_id' => $manpower->id,
        ]);

        return $scheduleAbsence;
    }
}
