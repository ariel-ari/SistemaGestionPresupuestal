<?php

namespace App\Services;

use App\Models\Office;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service para lógica de negocio de Offices (Centros de Costo).
 *
 * Centraliza operaciones complejas que involucran múltiples modelos
 * o lógica de negocio que no pertenece directamente al modelo.
 */
class OfficeService
{
    /**
     * Crea un nuevo centro de costo con su subunidad principal.
     *
     * La subunidad principal se crea automáticamente vía Observer,
     * pero este método puede extenderse para lógica adicional.
     *
     * @param  array<string, mixed>  $data  Datos del centro de costo
     *
     * @throws \Exception
     */
    public function create(array $data): Office
    {
        try {
            DB::beginTransaction();

            $office = Office::create($data);

            // El Observer ya crea la subunidad principal
            // Aquí podríamos agregar lógica adicional si es necesario

            Log::info('Centro de costo creado', [
                'office_id' => $office->id,
                'code' => $office->code,
                'name' => $office->name,
            ]);

            DB::commit();

            return $office->fresh(['subunits']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear centro de costo', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Actualiza un centro de costo.
     *
     * Si el nombre cambia, el Observer sincroniza automáticamente
     * la subunidad principal del sistema.
     *
     * @param  Office  $office  Centro de costo a actualizar
     * @param  array<string, mixed>  $data  Nuevos datos
     *
     * @throws \Exception
     */
    public function update(Office $office, array $data): Office
    {
        try {
            DB::beginTransaction();

            $oldName = $office->name;
            $office->update($data);

            // El Observer maneja la sincronización de la subunidad

            if ($oldName !== $office->name) {
                Log::info('Centro de costo actualizado con cambio de nombre', [
                    'office_id' => $office->id,
                    'old_name' => $oldName,
                    'new_name' => $office->name,
                ]);
            }

            DB::commit();

            return $office->fresh(['subunits']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar centro de costo', [
                'office_id' => $office->id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Elimina (soft delete) un centro de costo.
     *
     * Valida que no tenga relaciones críticas antes de eliminar.
     *
     * @param  Office  $office  Centro de costo a eliminar
     *
     * @throws \Exception
     */
    public function delete(Office $office): bool
    {
        try {
            // Validar que no tenga relaciones críticas
            if ($office->officeBudgetAssignments()->exists()) {
                throw new \Exception('No se puede eliminar el centro de costo porque tiene asignaciones presupuestales.');
            }

            if ($office->simulatedExpenses()->exists()) {
                throw new \Exception('No se puede eliminar el centro de costo porque tiene gastos simulados.');
            }

            DB::beginTransaction();

            // Soft delete de subunidades relacionadas
            $office->subunits()->delete();

            $deleted = $office->delete();

            Log::info('Centro de costo eliminado', [
                'office_id' => $office->id,
                'code' => $office->code,
            ]);

            DB::commit();

            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al eliminar centro de costo', [
                'office_id' => $office->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Activa o desactiva un centro de costo.
     *
     * @param  Office  $office  Centro de costo
     * @param  bool  $isActive  Nuevo estado
     */
    public function toggleStatus(Office $office, bool $isActive): Office
    {
        $office->update(['is_active' => $isActive]);

        Log::info('Estado de centro de costo modificado', [
            'office_id' => $office->id,
            'is_active' => $isActive,
        ]);

        return $office->fresh();
    }

    /**
     * Obtiene centros de costo activos con conteo de subunidades.
     *
     * @return Collection<int, Office>
     */
    public function getActiveWithSubunitsCount(): Collection
    {
        return Office::active()
            ->withSubunitsCount()
            ->orderByCode()
            ->get();
    }

    /**
     * Busca centros de costo por término.
     *
     * @param  string  $search  Término de búsqueda
     * @param  bool  $onlyActive  Filtrar solo activos
     * @return Collection<int, Office>
     */
    public function search(string $search, bool $onlyActive = true): Collection
    {
        $query = Office::search($search);

        if ($onlyActive) {
            $query->active();
        }

        return $query->orderByCode()->get();
    }

    /**
     * Obtiene estadísticas de un centro de costo.
     *
     * @param  Office  $office  Centro de costo
     * @return array<string, mixed>
     */
    public function getStatistics(Office $office): array
    {
        return [
            'total_subunits' => $office->subunits()->count(),
            'active_subunits' => $office->subunits()->active()->count(),
            'system_subunits' => $office->subunits()->systemGenerated()->count(),
            'user_subunits' => $office->subunits()->userCreated()->count(),
            'budget_assignments' => $office->officeBudgetAssignments()->count(),
            'simulated_expenses' => $office->simulatedExpenses()->count(),
        ];
    }
}
