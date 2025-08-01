<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    //
    protected $guarded = [];

    public function manpower()
    {
        return $this->hasOne(RelationAbsenceManpower::class);
    }

   public function schedule()
    {
        return $this->hasOne(RelationAbsenceSchedule::class);
    }
}
