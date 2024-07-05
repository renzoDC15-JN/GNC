<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Projects;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use App\Models\Locations;
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;
    use HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@joy-nostalg.com') ;
    }

    public function projects()
    {
        return $this->belongsToMany(Projects::class, 'user_projects', 'user_id', 'project_code', 'id', 'code');
    }

    public function locations()
    {
        return $this->belongsToMany(Locations::class, 'user_locations', 'user_id', 'location_code', 'id', 'code');
    }


}
