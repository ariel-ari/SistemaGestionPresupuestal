<?php

namespace App\Observers;

use App\Models\Office;
use Illuminate\Support\Facades\Log;

/**
 * Observer para el modelo Office.
 *
 * Maneja eventos del ciclo de vida de Office para:
 * - Crear automáticamente la subunidad principal al crear un Office
 * - Sincronizar el nombre de la subunidad principal al actualizar el Office
 * - Gestionar soft deletes de subunidades relacionadas
 */
class OfficeObserver
{
    /**
     * Handle the Office "created" event.
     *
     * Crea automáticamente una subunidad principal con el mismo nombre
     * del Office y marcada como generada por el sistema.
     */
    public function created(Office $office): void
    {
        try {
            $office->subunits()->create([
                'name' => $office->name,
                'is_active' => true,
                'is_system' => true,
            ]);

            Log::info('Subunidad principal creada automáticamente', [
                'office_id' => $office->id,
                'office_name' => $office->name,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear subunidad principal automáticamente', [
                'office_id' => $office->id,
                'error' => $e->getMessage(),
            ]);

            // Re-lanzar la excepción para que la transacción falle
            throw $e;
        }
    }

    /**
     * Handle the Office "updated" event.
     *
     * Si el nombre del Office cambió, sincroniza el nombre de la subunidad
     * principal del sistema.
     */
    public function updated(Office $office): void
    {
        // Solo procesar si el nombre cambió
        if (! $office->wasChanged('name')) {
            return;
        }

        try {
            // Buscar la subunidad principal usando firstWhere (más eficiente)
            $subunit = $office->subunits()
                ->where('is_system', true)
                ->first();

            if ($subunit) {
                $oldName = $subunit->name;

                $subunit->update([
                    'name' => $office->name,
                ]);

                Log::info('Subunidad principal sincronizada', [
                    'office_id' => $office->id,
                    'subunit_id' => $subunit->id,
                    'old_name' => $oldName,
                    'new_name' => $office->name,
                ]);
            } else {
                Log::warning('No se encontró subunidad principal para sincronizar', [
                    'office_id' => $office->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error al sincronizar subunidad principal', [
                'office_id' => $office->id,
                'error' => $e->getMessage(),
            ]);

            // No re-lanzar para no bloquear la actualización del Office
            // La sincronización puede hacerse manualmente si falla
        }
    }

    /**
     * Handle the Office "deleting" event.
     *
     * Realiza soft delete de todas las subunidades relacionadas
     * cuando se elimina un Office.
     */
    public function deleting(Office $office): void
    {
        try {
            // Soft delete de todas las subunidades
            $deletedCount = $office->subunits()->delete();

            Log::info('Subunidades eliminadas con el Office', [
                'office_id' => $office->id,
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar subunidades del Office', [
                'office_id' => $office->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle the Office "deleted" event.
     */
    public function deleted(Office $office): void
    {
        Log::info('Office eliminado', [
            'office_id' => $office->id,
            'code' => $office->code,
        ]);
    }

    /**
     * Handle the Office "restoring" event.
     *
     * Restaura las subunidades cuando se restaura un Office.
     */
    public function restoring(Office $office): void
    {
        try {
            // Restaurar subunidades eliminadas
            $office->subunits()->onlyTrashed()->restore();

            Log::info('Subunidades restauradas con el Office', [
                'office_id' => $office->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al restaurar subunidades del Office', [
                'office_id' => $office->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle the Office "restored" event.
     */
    public function restored(Office $office): void
    {
        Log::info('Office restaurado', [
            'office_id' => $office->id,
            'code' => $office->code,
        ]);
    }

    /**
     * Handle the Office "force deleted" event.
     *
     * Elimina permanentemente las subunidades cuando se hace
     * force delete de un Office.
     */
    public function forceDeleting(Office $office): void
    {
        try {
            // Force delete de todas las subunidades (incluso soft deleted)
            $office->subunits()->withTrashed()->forceDelete();

            Log::warning('Subunidades eliminadas permanentemente con el Office', [
                'office_id' => $office->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar permanentemente subunidades', [
                'office_id' => $office->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle the Office "force deleted" event.
     */
    public function forceDeleted(Office $office): void
    {
        Log::warning('Office eliminado permanentemente', [
            'office_id' => $office->id,
            'code' => $office->code,
        ]);
    }
}
