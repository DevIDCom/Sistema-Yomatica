<?php

namespace App\Domains\Users\Models;

use App\Domains\Tickets\Infrastructure\Eloquent\Models\FsmTicket;
use App\Models\InventoryWarehouse;
use App\Models\PwaSyncLog;
use App\Models\StatusHistory;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles;
    use Notifiable;
    use HasFactory;

    protected static function newFactory(): UserFactory{
        return UserFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function scopeOnlyTechnicians($query)
    {
        return $query->role('tecnico');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['super_admin', 'admin']);
        }

        if ($panel->getId() === 'technician') {
            return $this->hasAnyRole(['tecnico', 'super_admin']);
        }

        return false;
    }

    public function warehouse(): HasOne
    {
        return $this->hasOne(InventoryWarehouse::class, 'assigned_tech_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(FsmTicket::class, 'assigned_tech_id');
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(PwaSyncLog::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }
}
