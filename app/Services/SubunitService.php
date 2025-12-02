<?php

namespace App\Services;

use App\Models\Office;
use App\Models\Subunit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service para lógica de negocio de Subunits (Finalidades).
 *
 * Maneja operaciones de subunidades con protección especial
 * para las generadas automáticamente por el sistema.
 */
class SubunitService
{
    /**
     * Crea una nueva subunidad para un centro de costo.
     *
     * @param  int  $officeId  ID del centro de costo
     * @param  array<string, mixed>  $data  Datos de la subunidad
     *
     * @throws \Exception
     */
    public function create(int $officeId, array $data): Subunit
    {
        try {
            // Verificar que el office existe
            $office = Office::findOrFail($officeId);

            DB::beginTransaction();

            $data['office_id'] = $officeId;
            $data['is_system'] = false; // Las creadas manualmente nunca son del sistema

            $subunit = Subunit::create($data);

            Log::info('Subunidad creada', [
                'subunit_id' => $subunit->id,
                'office_id' => $officeId,
                'name' => $subunit->name,
            ]);

            DB::commit();

            return $subunit->fresh(['office']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear subunidad', [
                'office_id' => $officeId,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Actualiza una subunidad.
     *
     * Valida que no sea una subunidad del sistema.
     *
     * @param  Subunit  $subunit  Subunidad a actualizar
     * @param  array<string, mixed>  $data  Nuevos datos
     *
     * @throws \Exception
     */
    public function update(Subunit $subunit, array $data): Subunit
    {
        // Proteger subunidades del sistema
        if ($subunit->is_system) {
            throw new \Exception('No se pueden editar subunidades generadas por el sistema. Estas se sincronizan automáticamente con el centro de costo.');
        }

        try {
            DB::beginTransaction();

            $subunit->update($data);

            Log::info('Subunidad actualizada', [
                'subunit_id' => $subunit->id,
                'office_id' => $subunit->office_id,
            ]);

            DB::commit();

            return $subunit->fresh(['office']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar subunidad', [
                'subunit_id' => $subunit->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Elimina una subunidad.
     *
     * Valida que no sea una subunidad del sistema y que no tenga
     * relaciones críticas.
     *
     * @param  Subunit  $subunit  Subunidad a eliminar
     *
     * @throws \Exception
     */
    public function delete(Subunit $subunit): bool
    {
        // Proteger subunidades del sistema
        if ($subunit->is_system) {
            throw new \Exception('No se pueden eliminar subunidades generadas por el sistema.');
        }

        try {
            // Validar que no tenga relaciones críticas
            if ($subunit->officeBudgetAssignments()->exists()) {
                throw new \Exception('No se puede eliminar la subunidad porque tiene asignaciones presupuestales.');
            }

            DB::beginTransaction();

            $deleted = $subunit->delete();

            Log::info('Subunidad eliminada', [
                'subunit_id' => $subunit->id,
                'office_id' => $subunit->office_id,
            ]);

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar subunidad', [
                'subunit_id' => $subunit->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Activa o desactiva una subunidad.
     *
     * Las subunidades del sistema no pueden cambiar de estado manualmente.
     *
     * @param  Subunit  $subunit  Subunidad
     * @param  bool  $isActive  Nuevo estado
     *
     * @throws \Exception
     */
    public function toggleStatus(Subunit $subunit, bool $isActive): Subunit
    {
        if ($subunit->is_system) {
            throw new \Exception('El estado de las subunidades del sistema se sincroniza automáticamente con el centro de costo.');
        }

        $subunit->update(['is_active' => $isActive]);

        Log::info('Estado de subunidad modificado', [
            'subunit_id' => $subunit->id,
            'is_active' => $isActive,
        ]);

        return $subunit->fresh();
    }

    /**
     * Obtiene subunidades activas de un centro de costo.
     *
     * @param  int  $officeId  ID del centro de costo
     * @param  bool  $includeSystem  Incluir subunidades del sistema
     * @return Collection<int, Subunit>
     */
    public function getActiveByOffice(int $officeId, bool $includeSystem = true): Collection
    {
        $query = Subunit::forOffice($officeId)->active();

        if (! $includeSystem) {
            $query->userCreated();
        }

        return $query->get();
    }

    /**
     * Obtiene todas las subunidades de un centro de costo.
     *
     * @param  int  $officeId  ID del centro de costo
     * @return Collection<int, Subunit>
     */
    public function getAllByOffice(int $officeId): Collection
    {
        return Subunit::forOffice($officeId)
            ->withOffice()
            ->get();
    }

    /**
     * Busca subunidades por término en un centro de costo específico.
     *
     * @param  int  $officeId  ID del centro de costo
     * @param  string  $search  Término de búsqueda
     * @param  bool  $onlyActive  Filtrar solo activas
     * @return Collection<int, Subunit>
     */
    public function search(int $officeId, string $search, bool $onlyActive = true): Collection
    {
        $query = Subunit::forOffice($officeId)->search($search);

        if ($onlyActive) {
            $query->active();
        }

        return $query->get();
    }

    /**
     * Obtiene la subunidad principal del sistema de un centro de costo.
     *
     * @param  int  $officeId  ID del centro de costo
     */
    public function getSystemSubunit(int $officeId): ?Subunit
    {
        return Subunit::forOffice($officeId)
            ->systemGenerated()
            ->first();
    }

    /**
     * Verifica si una subunidad puede ser editada.
     *
     * @param  Subunit  $subunit  Subunidad
     */
    public function canEdit(Subunit $subunit): bool
    {
        return ! $subunit->is_system;
    }

    /**
     * Verifica si una subunidad puede ser eliminada.
     *
     * @param  Subunit  $subunit  Subunidad
     */
    public function canDelete(Subunit $subunit): bool
    {
        if ($subunit->is_system) {
            return false;
        }

        // Verificar relaciones críticas
        return ! $subunit->officeBudgetAssignments()->exists();
    }
}
