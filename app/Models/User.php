<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */

    use HasFactory, Notifiable, HasApiTokens, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
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

    public function canAccessPanel(Panel $panel): bool
    {
        if (Auth::user()->role === 'Admin' || Auth::user()->role === 'Super Admin') {
            return true;
        }

        return false;
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function projectPins(): HasMany
    {
        return $this->hasMany(ProjectPin::class);
    }

    public function subscribed(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

}
