<?php

namespace App\Policies;

use App\Models\Subunit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy para autorización de operaciones sobre el modelo Subunit.
 *
 * Implementa protección especial para subunidades generadas automáticamente
 * por el sistema (is_system = true) que no deben ser editables manualmente.
 */
class SubunitPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver la lista de subunidades/finalidades.
     *
     * @param  User  $user  Usuario autenticado
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view offices');
    }

    /**
     * Determina si el usuario puede ver una subunidad específica.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Subunit  $subunit  Subunidad a visualizar
     */
    public function view(User $user, Subunit $subunit): bool
    {
        return $user->can('view offices');
    }

    /**
     * Determina si el usuario puede crear subunidades.
     *
     * @param  User  $user  Usuario autenticado
     */
    public function create(User $user): bool
    {
        return $user->can('create offices');
    }

    /**
     * Determina si el usuario puede actualizar una subunidad.
     *
     * Validaciones:
     * - No se pueden editar subunidades generadas por el sistema (is_system = true)
     * - Estas se sincronizan automáticamente con el Office
     *
     * @param  User  $user  Usuario autenticado
     * @param  Subunit  $subunit  Subunidad a actualizar
     */
    public function update(User $user, Subunit $subunit): bool
    {
        if (! $user->can('edit offices')) {
            return false;
        }

        // Proteger subunidades del sistema de edición manual
        if ($subunit->is_system) {
            return false;
        }

        return true;
    }

    /**
     * Determina si el usuario puede eliminar una subunidad.
     *
     * Validaciones:
     * - No se pueden eliminar subunidades del sistema
     * - No se puede eliminar si tiene asignaciones presupuestales
     * - No se puede eliminar si tiene gastos simulados
     *
     * @param  User  $user  Usuario autenticado
     * @param  Subunit  $subunit  Subunidad a eliminar
     */
    public function delete(User $user, Subunit $subunit): bool
    {
        if (! $user->can('delete offices')) {
            return false;
        }

        // Proteger subunidades del sistema de eliminación
        if ($subunit->is_system) {
            return false;
        }

        // Validar que no tenga relaciones críticas
        // TODO: Agregar validación cuando se implementen las relaciones
        // $hasActiveBudgets = $subunit->budgetAssignments()->exists();
        // $hasSimulatedExpenses = $subunit->simulatedExpenses()->exists();
        // return !$hasActiveBudgets && !$hasSimulatedExpenses;

        return true;
    }

    /**
     * Determina si el usuario puede restaurar una subunidad eliminada.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Subunit  $subunit  Subunidad a restaurar
     */
    public function restore(User $user, Subunit $subunit): bool
    {
        return $user->can('delete offices');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente una subunidad.
     *
     * Solo Super Admins pueden hacer eliminación permanente.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Subunit  $subunit  Subunidad a eliminar permanentemente
     */
    public function forceDelete(User $user, Subunit $subunit): bool
    {
        if ($subunit->is_system) {
            return false;
        }

        return $user->can('delete offices') && $user->hasRole('Super Admin');
    }

    /**
     * Determina si el usuario puede activar/desactivar una subunidad.
     *
     * Las subunidades del sistema pueden ser desactivadas pero su estado
     * se sincroniza con el Office padre.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Subunit  $subunit  Subunidad a modificar estado
     */
    public function toggleStatus(User $user, Subunit $subunit): bool
    {
        if (! $user->can('edit offices')) {
            return false;
        }

        // Las subunidades del sistema solo pueden cambiar estado
        // a través del Office padre
        if ($subunit->is_system) {
            return false;
        }

        return true;
    }
}
