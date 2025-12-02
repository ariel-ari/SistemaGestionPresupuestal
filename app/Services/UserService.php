<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Service para lógica de negocio de Users (Usuarios).
 *
 * Maneja operaciones complejas de usuarios incluyendo
 * gestión de roles, validaciones de seguridad y auditoría.
 */
class UserService
{
    /**
     * Crea un nuevo usuario con rol asignado.
     *
     * @param  array<string, mixed>  $data  Datos del usuario
     * @param  int  $roleId  ID del rol a asignar
     *
     * @throws \Exception
     */
    public function create(array $data, int $roleId): User
    {
        try {
            DB::beginTransaction();

            // Hashear contraseña si no está hasheada
            if (isset($data['password']) && ! Hash::needsRehash($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = User::create($data);
            $user->syncRoles($roleId);

            Log::info('Usuario creado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $roleId,
            ]);

            DB::commit();

            return $user->fresh(['roles']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear usuario', [
                'email' => $data['email'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Actualiza un usuario y su rol.
     *
     * @param  User  $user  Usuario a actualizar
     * @param  array<string, mixed>  $data  Nuevos datos
     * @param  int|null  $roleId  Nuevo rol (opcional)
     *
     * @throws \Exception
     */
    public function update(User $user, array $data, ?int $roleId = null): User
    {
        try {
            DB::beginTransaction();

            // Si hay contraseña nueva, hashearla
            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                // Si está vacía, no actualizarla
                unset($data['password']);
            }

            $user->update($data);

            // Actualizar rol si se proporcionó
            if ($roleId !== null) {
                $user->syncRoles($roleId);
            }

            Log::info('Usuario actualizado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_updated' => $roleId !== null,
            ]);

            DB::commit();

            return $user->fresh(['roles']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar usuario', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Desactiva un usuario (soft disable).
     *
     * Valida que no sea el último Super Admin activo.
     *
     * @param  User  $user  Usuario a desactivar
     *
     * @throws \Exception
     */
    public function deactivate(User $user): User
    {
        // Validar que no sea el último Super Admin
        if ($user->hasRole('Super Admin')) {
            $activeSuperAdmins = User::role('Super Admin')
                ->where('is_active', true)
                ->count();

            if ($activeSuperAdmins <= 1) {
                throw new \Exception('No se puede desactivar el último Super Admin del sistema.');
            }
        }

        $user->update(['is_active' => false]);

        Log::warning('Usuario desactivado', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $user->fresh();
    }

    /**
     * Activa un usuario.
     *
     * @param  User  $user  Usuario a activar
     */
    public function activate(User $user): User
    {
        $user->update(['is_active' => true]);

        Log::info('Usuario activado', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $user->fresh();
    }

    /**
     * Elimina un usuario (soft delete).
     *
     * Valida que no sea el último Super Admin y que no se auto-elimine.
     *
     * @param  User  $user  Usuario a eliminar
     * @param  User  $currentUser  Usuario que ejecuta la acción
     *
     * @throws \Exception
     */
    public function delete(User $user, User $currentUser): bool
    {
        // Prevenir auto-eliminación
        if ($user->id === $currentUser->id) {
            throw new \Exception('No puedes eliminar tu propia cuenta.');
        }

        // Validar que no sea el último Super Admin
        if ($user->hasRole('Super Admin')) {
            $activeSuperAdmins = User::role('Super Admin')
                ->where('is_active', true)
                ->count();

            if ($activeSuperAdmins <= 1) {
                throw new \Exception('No se puede eliminar el último Super Admin del sistema.');
            }
        }

        try {
            DB::beginTransaction();

            $deleted = $user->delete();

            Log::warning('Usuario eliminado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'deleted_by' => $currentUser->id,
            ]);

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar usuario', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Obtiene usuarios activos con sus roles.
     *
     * @return Collection<int, User>
     */
    public function getActiveUsers(): Collection
    {
        return User::active()
            ->with('roles')
            ->orderByName()
            ->get();
    }

    /**
     * Busca usuarios por término.
     *
     * @param  string  $search  Término de búsqueda
     * @param  bool  $onlyActive  Filtrar solo activos
     * @return Collection<int, User>
     */
    public function search(string $search, bool $onlyActive = true): Collection
    {
        $query = User::search($search);

        if ($onlyActive) {
            $query->active();
        }

        return $query->with('roles')->orderByName()->get();
    }

    /**
     * Obtiene usuarios por rol.
     *
     * @param  string  $roleName  Nombre del rol
     * @param  bool  $onlyActive  Filtrar solo activos
     * @return Collection<int, User>
     */
    public function getUsersByRole(string $roleName, bool $onlyActive = true): Collection
    {
        $query = User::role($roleName);

        if ($onlyActive) {
            $query->active();
        }

        return $query->with('roles')->orderByName()->get();
    }

    /**
     * Cambia la contraseña de un usuario.
     *
     * @param  User  $user  Usuario
     * @param  string  $newPassword  Nueva contraseña
     */
    public function changePassword(User $user, string $newPassword): User
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        Log::info('Contraseña cambiada', [
            'user_id' => $user->id,
        ]);

        return $user->fresh();
    }
}
