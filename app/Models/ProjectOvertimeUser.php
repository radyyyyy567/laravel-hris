<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectOvertimeUser extends Model
{
    protected $guarded = [];
    //

    public function overtime()
    {
        return $this->belongsTo(OvertimeAssignment::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
