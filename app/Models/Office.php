<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Office - Centros de Costo
 *
 * Representa los centros de costo de la institución.
 * Cada Office puede tener múltiples Subunits (finalidades).
 *
 * @property int $id
 * @property string $code Código único del centro de costo
 * @property string $name Nombre del centro de costo
 * @property string|null $short_name Nombre corto
 * @property string|null $description Descripción
 * @property bool $is_active Estado activo/inactivo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Office extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'short_name',
        'description',
        'is_active',
    ];

    /**
     * Relaciones a cargar automáticamente (eager loading).
     * Previene problema N+1 en queries frecuentes.
     *
     * @var array<int, string>
     */
    protected $with = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'code' => 'string',
            'name' => 'string',
            'short_name' => 'string',
            'description' => 'string',
            'is_active' => 'boolean',
            'id' => 'integer',
        ];
    }

    /**
     * Relación: Subunidades (finalidades) del centro de costo.
     *
     * @return HasMany<Subunit>
     */
    public function subunits(): HasMany
    {
        return $this->hasMany(Subunit::class);
    }

    /**
     * Relación: Asignaciones presupuestales del centro de costo.
     *
     * @return HasMany<OfficeBudgetAssignment>
     */
    public function officeBudgetAssignments(): HasMany
    {
        return $this->hasMany(OfficeBudgetAssignment::class);
    }

    /**
     * Relación: Gastos simulados del centro de costo.
     *
     * @return HasMany<SimulatedExpense>
     */
    public function simulatedExpenses(): HasMany
    {
        return $this->hasMany(SimulatedExpense::class);
    }

    /**
     * Scope: Filtrar solo centros de costo activos.
     *
     * @param  Builder<Office>  $query
     * @return Builder<Office>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filtrar solo centros de costo inactivos.
     *
     * @param  Builder<Office>  $query
     * @return Builder<Office>
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: Incluir conteo de subunidades.
     * Útil para mostrar estadísticas sin N+1 queries.
     *
     * @param  Builder<Office>  $query
     * @return Builder<Office>
     */
    public function scopeWithSubunitsCount(Builder $query): Builder
    {
        return $query->withCount('subunits');
    }

    /**
     * Scope: Buscar por código o nombre.
     *
     * @param  Builder<Office>  $query
     * @param  string  $search  Término de búsqueda
     * @return Builder<Office>
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Ordenar por código.
     *
     * @param  Builder<Office>  $query
     * @param  string  $direction  Dirección del ordenamiento (asc|desc)
     * @return Builder<Office>
     */
    public function scopeOrderByCode(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('code', $direction);
    }

    /**
     * Scope: Cargar subunidades activas.
     *
     * @param  Builder<Office>  $query
     * @return Builder<Office>
     */
    public function scopeWithActiveSubunits(Builder $query): Builder
    {
        return $query->with(['subunits' => function (HasMany $query) {
            $query->where('is_active', true);
        }]);
    }
}
