<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelationScheduleManpower extends Model
{
    protected $guarded = [];
    //
    public function schedule()
    {
        return $this->belongsTo(ScheduleAbsence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
