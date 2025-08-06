<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelationNotificationUser extends Model
{
    protected $guarded = [];
    //
    public function notif()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
