<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //

    protected $guarded = [];

    public function pic()
    {
        return $this->hasOne(ProjectPic::class);
    }

    public function manpower()
    {
        return $this->hasMany(ProjectManpower::class);
    }

    public function overtimeuser()
    {
        return $this->hasMany(related: ProjectOvertimeUser::class);
    }

     public function placement()
    {
        return $this->hasOne(RelationPlacementProject::class);
    }


       public function scopeActive($query)
    {
        return $query->where('status', 'work');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    // Generate URL based on project ID or name
    public function getUrlAttribute()
    {
        return '/admin/projects/' . $this->id;
    }

    // Get icon based on status or use default
    public function getIconAttribute()
    {
        return match($this->status) {
            'active' => 'heroicon-o-check-circle',
            'completed' => 'heroicon-o-check-badge',
            'pending' => 'heroicon-o-clock',
            'cancelled' => 'heroicon-o-x-circle',
            default => 'heroicon-o-folder'
        };
    }
}
