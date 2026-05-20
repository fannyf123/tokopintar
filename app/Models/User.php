<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_KASIR = 'kasir';
    public const ROLE_GUDANG = 'gudang';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_KASIR,
        self::ROLE_GUDANG,
    ];

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'aktif',
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
            'aktif' => 'boolean',
        ];
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKasir(): bool
    {
        return $this->role === self::ROLE_KASIR;
    }

    public function isGudang(): bool
    {
        return $this->role === self::ROLE_GUDANG;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
