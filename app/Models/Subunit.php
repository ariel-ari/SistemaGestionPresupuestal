<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Subunit - Finalidades/Subunidades
 *
 * Representa las finalidades o subunidades de un centro de costo (Office).
 * Algunas subunidades son generadas automáticamente por el sistema (is_system = true)
 * y se sincronizan con el Office padre.
 *
 * @property int $id
 * @property int $office_id ID del centro de costo padre
 * @property string $name Nombre de la finalidad
 * @property string|null $description Descripción
 * @property bool $is_active Estado activo/inactivo
 * @property bool $is_system Indica si fue generada por el sistema
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Office $office Centro de costo padre
 */
class Subunit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'office_id',
        'name',
        'description',
        'is_active',
        'is_system',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'office_id' => 'integer',
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    /**
     * Relación: Centro de costo al que pertenece la subunidad.
     *
     * @return BelongsTo<Office, Subunit>
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Relación: Asignaciones presupuestales de la subunidad.
     *
     * @return HasMany<OfficeBudgetAssignment>
     */
    public function officeBudgetAssignments(): HasMany
    {
        return $this->hasMany(OfficeBudgetAssignment::class);
    }

    /**
     * Scope: Filtrar solo subunidades activas.
     *
     * @param  Builder<Subunit>  $query
     * @return Builder<Subunit>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filtrar solo subunidades inactivas.
     *
     * @param  Builder<Subunit>  $query
     * @return Builder<Subunit>
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: Filtrar solo subunidades generadas por el sistema.
     *
     * Estas subunidades se crean automáticamente al crear un Office
     * y se sincronizan con el nombre del Office.
     *
     * @param  Builder<Subunit>  $query
     * @return Builder<Subunit>
     */
    public function scopeSystemGenerated(Builder $query): Builder
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope: Filtrar solo subunidades creadas manualmente por usuarios.
     *
     * @param  Builder<Subunit>  $query
     * @return Builder<Subunit>
     */
    public function scopeUserCreated(Builder $query): Builder
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope: Filtrar subunidades de un centro de costo específico.
     *
     * @param  Builder<Subunit>  $query
     * @param  int  $officeId  ID del centro de costo
     * @return Builder<Subunit>
     */
    public function scopeForOffice(Builder $query, int $officeId): Builder
    {
        return $query->where('office_id', $officeId);
    }

    /**
     * Scope: Buscar por nombre.
     *
     * @param  Builder<Subunit>  $query
     * @param  string  $search  Término de búsqueda
     * @return Builder<Subunit>
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope: Cargar el centro de costo padre.
     *
     * @param  Builder<Subunit>  $query
     * @return Builder<Subunit>
     */
    public function scopeWithOffice(Builder $query): Builder
    {
        return $query->with('office');
    }

    /**
     * Verifica si la subunidad fue generada por el sistema.
     */
    public function isSystemGenerated(): bool
    {
        return $this->is_system === true;
    }

    /**
     * Verifica si la subunidad puede ser editada manualmente.
     *
     * Las subunidades del sistema no pueden editarse manualmente.
     */
    public function isEditable(): bool
    {
        return $this->is_system === false;
    }
}
