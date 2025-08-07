<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->hasMany(RelationPlacementUser::class);
    }

        public function project()
    {
        return $this->hasOne(RelationPlacementProject::class);
    }

    public function users()
{
    return $this->hasManyThrough(
        User::class,
        RelationPlacementUser::class,
        'placement_id', // Foreign key on RelationPlacementUser
        'id',           // Foreign key on User
        'id',           // Local key on Placement
        'user_id'       // Local key on RelationPlacementUser
    );
}


}
