<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RelationAbsenceManpower extends Model
{
    //
    protected $guarded = [];

    public function absence()
    {
        return $this->belongsTo(Absence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
