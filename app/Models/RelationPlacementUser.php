<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;

class RelationPlacementUser extends Model
{
    //
    protected $guarded = [];

    public function placement()
    {
        return $this->belongsTo(related: Placement::class);
    }

    public function user()
    {
        return $this->belongsTo(related: User::class);
    }
}
