<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'photo',
        'is_active',
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
            'is_active'         => 'boolean',
        ];
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "User {$this->name} has been {$eventName}");
    }

    // ==================
    // RELASI
    // ==================
    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class, 'user_id');
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class, 'user_id');
    }

    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class, 'user_id');
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class, 'user_id');
    }

    public function eoqCalculations(): HasMany
    {
        return $this->hasMany(EoqCalculation::class, 'calculated_by');
    }

    public function forecasts(): HasMany
    {
        return $this->hasMany(Forecast::class, 'generated_by');
    }

    // ==================
    // ACCESSOR
    // ==================
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-avatar.png');
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->getRoleNames()->first()) {
            'admin'   => 'Admin Gudang',
            'manajer' => 'Manajer',
            'user'    => 'User',
            default   => '-',
        };
    }
}
