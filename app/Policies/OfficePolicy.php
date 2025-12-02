<?php

namespace App\Policies;

use App\Models\Office;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy para autorización de operaciones sobre el modelo Office.
 *
 * Controla el acceso a operaciones CRUD basándose en los permisos
 * del usuario autenticado usando Spatie Permission.
 */
class OfficePolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver la lista de centros de costo.
     *
     * @param  User  $user  Usuario autenticado
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view offices');
    }

    /**
     * Determina si el usuario puede ver un centro de costo específico.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Office  $office  Centro de costo a visualizar
     */
    public function view(User $user, Office $office): bool
    {
        return $user->can('view offices');
    }

    /**
     * Determina si el usuario puede crear centros de costo.
     *
     * @param  User  $user  Usuario autenticado
     */
    public function create(User $user): bool
    {
        return $user->can('create offices');
    }

    /**
     * Determina si el usuario puede actualizar un centro de costo.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Office  $office  Centro de costo a actualizar
     */
    public function update(User $user, Office $office): bool
    {
        return $user->can('edit offices');
    }

    /**
     * Determina si el usuario puede eliminar un centro de costo.
     *
     * Validaciones adicionales:
     * - No se puede eliminar si tiene asignaciones presupuestales activas
     * - No se puede eliminar si tiene gastos simulados
     *
     * @param  User  $user  Usuario autenticado
     * @param  Office  $office  Centro de costo a eliminar
     */
    public function delete(User $user, Office $office): bool
    {
        if (! $user->can('delete offices')) {
            return false;
        }

        // Validar que no tenga relaciones críticas
        $hasActiveBudgets = $office->officeBudgetAssignments()->exists();
        $hasSimulatedExpenses = $office->simulatedExpenses()->exists();

        return ! $hasActiveBudgets && ! $hasSimulatedExpenses;
    }

    /**
     * Determina si el usuario puede restaurar un centro de costo eliminado.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Office  $office  Centro de costo a restaurar
     */
    public function restore(User $user, Office $office): bool
    {
        return $user->can('delete offices');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un centro de costo.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Office  $office  Centro de costo a eliminar permanentemente
     */
    public function forceDelete(User $user, Office $office): bool
    {
        return $user->can('delete offices') && $user->hasRole('Super Admin');
    }

    /**
     * Determina si el usuario puede activar/desactivar un centro de costo.
     *
     * @param  User  $user  Usuario autenticado
     * @param  Office  $office  Centro de costo a modificar estado
     */
    public function toggleStatus(User $user, Office $office): bool
    {
        return $user->can('edit offices');
    }
}
