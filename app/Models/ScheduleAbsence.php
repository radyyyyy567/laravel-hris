<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleAbsence extends Model
{
    //
    protected $guarded = [];

    public function manpower()
    {
        return $this->hasOne(RelationScheduleManpower::class, 'schedule_id');
    }
    public function absence()
    {
        return $this->hasOne(RelationAbsenceSchedule::class, 'schedule_id');
    }

}
