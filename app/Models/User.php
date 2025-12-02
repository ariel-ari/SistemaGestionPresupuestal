<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Modelo User - Usuarios del Sistema
 *
 * Representa los usuarios con autenticación y autorización.
 * Usa Spatie Permission para gestión de roles y permisos.
 *
 * @property int $id
 * @property string $name Nombre completo del usuario
 * @property string $email Correo electrónico único
 * @property string $password Contraseña hasheada
 * @property bool $is_active Estado activo/inactivo
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $role_name Nombre del primer rol del usuario
 * @property-read string $initials Iniciales del nombre
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * Relaciones a cargar automáticamente (eager loading).
     * Previene problema N+1 al acceder a roles.
     *
     * @var array<int, string>
     */
    protected $with = ['roles'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Obtiene las iniciales del nombre del usuario.
     *
     * Toma las primeras letras de las dos primeras palabras del nombre.
     * Ejemplo: "Juan Pérez" -> "JP"
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Accessor: Nombre del rol principal del usuario.
     *
     * Retorna el nombre del primer rol asignado o "Sin rol" si no tiene.
     *
     * @return Attribute<string, never>
     */
    protected function roleName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->roles->first()?->name ?? 'Sin rol'
        );
    }

    /**
     * Scope: Filtrar solo usuarios activos.
     *
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filtrar solo usuarios inactivos.
     *
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: Filtrar usuarios por rol.
     *
     * @param  Builder<User>  $query
     * @param  string  $roleName  Nombre del rol
     * @return Builder<User>
     */
    public function scopeWithRole(Builder $query, string $roleName): Builder
    {
        return $query->role($roleName);
    }

    /**
     * Scope: Buscar por nombre o email.
     *
     * @param  Builder<User>  $query
     * @param  string  $search  Término de búsqueda
     * @return Builder<User>
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Ordenar por nombre.
     *
     * @param  Builder<User>  $query
     * @param  string  $direction  Dirección del ordenamiento (asc|desc)
     * @return Builder<User>
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('name', $direction);
    }

    /**
     * Verifica si el usuario es Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Verifica si el usuario está activo.
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }
}
