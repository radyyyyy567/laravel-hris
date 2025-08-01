<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;

class RelationPlacementProject extends Model
{
    //
    protected $guarded = [];

    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
