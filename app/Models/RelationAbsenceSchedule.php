<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;

class RelationAbsenceSchedule extends Model
{
    //
    protected $guarded = [];

    public function absence()
    {
        return $this->belongsTo(Absence::class);
    }

    public function schedule()
    {
        return $this->belongsTo(ScheduleAbsence::class);
    }
}
