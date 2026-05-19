<?php

namespace App\Features\User\Models;

use App\Features\Pet\Models\Pet;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasUlids, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'dni',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'latitude',
        'longitude',
        'active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')
            ->using(UserRole::class)
            ->withPivot('asigned_at');
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function hasRole(RoleName|string $role): bool
    {
        $roleName = $role instanceof RoleName ? $role->value : $role;

        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('name', $roleName);
        }

        return $this->roles()->where('name', $roleName)->exists();
    }

    public function scopeWithRole(Builder $query, RoleName|string $role): Builder
    {
        $roleName = $role instanceof RoleName ? $role->value : $role;

        return $query->whereHas('roles', fn (Builder $query) => $query->where('name', $roleName));
    }

    public function primaryRole(): ?string
    {
        return $this->roles->first()?->name;
    }

    public function getJWTIdentifier(): string
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
