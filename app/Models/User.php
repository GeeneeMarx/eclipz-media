<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Gates access to the Filament /admin panel itself.
     * Both role AND active status are checked here — an inactive admin
     * cannot log in even with correct credentials.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->role === Role::Admin->value;
    }

    public function activityLogs()
    {
        return $this->hasMany(AdminActivityLog::class);
    }
}
