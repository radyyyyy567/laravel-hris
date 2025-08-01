<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;


class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pic()
    {
        return $this->hasMany(ProjectPic::class);
    }

    public function manpower()
    {
        return $this->hasMany(ProjectManpower::class);
    }

public function group()
    {
        return $this->hasOne(Group::class);
    }

    public function schedule()
    {
        return $this->hasMany(RelationScheduleManpower::class);
    }

    public function absence()
    {
        return $this->hasMany(RelationAbsenceManpower::class);
    }


    public function projects()
    {
        return $this->hasManyThrough(
            Project::class,
            ProjectManpower::class,
            'user_id',    // Foreign key on ProjectManpower
            'id',         // Foreign key on Project
            'id',         // Local key on User
            'project_id'  // Local key on ProjectManpower
        );
    }
    

    public function overtimeproject()
    {
        return $this->hasMany(related: ProjectOvertimeUser::class);
    }

    

    public function canAccessPanel(Panel $panel): bool
{
    // Only allow access to users whose email ends with @gmail.com
    // AND are verified, AND do NOT have the 'man_power' role
     if ($panel->getId() === 'admin') {
            return !$this->hasRole('man_power');
        }

        return true;
    
}


}
