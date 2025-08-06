<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeAssignment extends Model
{
    protected $guarded = [];
    //

    public function projectuser()
    {
        return $this->hasMany(related: ProjectOvertimeUser::class);
    }

    
    
}
