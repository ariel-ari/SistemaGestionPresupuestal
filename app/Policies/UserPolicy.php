<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy para autorización de operaciones sobre el modelo User.
 *
 * Implementa reglas de seguridad para prevenir:
 * - Auto-eliminación de usuarios
 * - Modificación de roles superiores al propio
 * - Desactivación del último Super Admin
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver la lista de usuarios.
     *
     * @param  User  $user  Usuario autenticado
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view users');
    }

    /**
     * Determina si el usuario puede ver un usuario específico.
     *
     * @param  User  $user  Usuario autenticado
     * @param  User  $model  Usuario a visualizar
     */
    public function view(User $user, User $model): bool
    {
        return $user->can('view users');
    }

    /**
     * Determina si el usuario puede crear usuarios.
     *
     * @param  User  $user  Usuario autenticado
     */
    public function create(User $user): bool
    {
        return $user->can('create users');
    }

    /**
     * Determina si el usuario puede actualizar otro usuario.
     *
     * Validaciones:
     * - No puede modificar usuarios con roles superiores
     * - Puede modificar su propio perfil (excepto rol)
     *
     * @param  User  $user  Usuario autenticado
     * @param  User  $model  Usuario a actualizar
     */
    public function update(User $user, User $model): bool
    {
        if (! $user->can('edit users')) {
            return false;
        }

        // Permitir edición de perfil propio (datos básicos)
        if ($user->id === $model->id) {
            return true;
        }

        // No permitir editar Super Admins si no eres Super Admin
        if ($model->hasRole('Super Admin') && ! $user->hasRole('Super Admin')) {
            return false;
        }

        return true;
    }

    /**
     * Determina si el usuario puede eliminar otro usuario.
     *
     * Validaciones:
     * - No puede auto-eliminarse
     * - No puede eliminar Super Admins si no es Super Admin
     * - No puede eliminar el último Super Admin del sistema
     *
     * @param  User  $user  Usuario autenticado
     * @param  User  $model  Usuario a eliminar
     */
    public function delete(User $user, User $model): bool
    {
        if (! $user->can('delete users')) {
            return false;
        }

        // Prevenir auto-eliminación
        if ($user->id === $model->id) {
            return false;
        }

        // Prevenir eliminación de Super Admins por no-Super Admins
        if ($model->hasRole('Super Admin') && ! $user->hasRole('Super Admin')) {
            return false;
        }

        // Prevenir eliminación del último Super Admin
        if ($model->hasRole('Super Admin')) {
            $superAdminCount = User::role('Super Admin')
                ->where('is_active', true)
                ->count();

            if ($superAdminCount <= 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determina si el usuario puede restaurar un usuario eliminado.
     *
     * @param  User  $user  Usuario autenticado
     * @param  User  $model  Usuario a restaurar
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can('delete users');
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un usuario.
     *
     * Solo Super Admins pueden hacer eliminación permanente.
     *
     * @param  User  $user  Usuario autenticado
     * @param  User  $model  Usuario a eliminar permanentemente
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('delete users') && $user->hasRole('Super Admin');
    }

    /**
     * Determina si el usuario puede activar/desactivar otro usuario.
     *
     * Validaciones:
     * - No puede auto-desactivarse
     * - No puede desactivar el último Super Admin
     *
     * @param  User  $user  Usuario autenticado
     * @param  User  $model  Usuario a modificar estado
     */
    public function toggleStatus(User $user, User $model): bool
    {
        if (! $user->can('edit users')) {
            return false;
        }

        // Prevenir auto-desactivación
        if ($user->id === $model->id) {
            return false;
        }

        // Prevenir desactivación del último Super Admin activo
        if ($model->hasRole('Super Admin') && $model->is_active) {
            $activeSuperAdminCount = User::role('Super Admin')
                ->where('is_active', true)
                ->count();

            if ($activeSuperAdminCount <= 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determina si el usuario puede asignar un rol específico.
     *
     * Regla: Solo puedes asignar roles de nivel igual o inferior al tuyo.
     *
     * @param  User  $user  Usuario autenticado
     * @param  string  $roleName  Nombre del rol a asignar
     */
    public function assignRole(User $user, string $roleName): bool
    {
        if (! $user->can('edit users')) {
            return false;
        }

        // Super Admin puede asignar cualquier rol
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // No-Super Admin no puede asignar rol de Super Admin
        if ($roleName === 'Super Admin') {
            return false;
        }

        return true;
    }
}
