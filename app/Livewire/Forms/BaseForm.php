<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

/**
 * BaseForm - Clase base para todos los formularios Livewire.
 *
 * Proporciona funcionalidad común para CRUD con:
 * - Autorización automática con Policies
 * - Hooks de ciclo de vida extensibles
 * - Manejo robusto de errores
 * - Sanitización de datos
 * - Logging estructurado
 * - Transacciones de base de datos
 *
 * @property Model|null $model Modelo asociado al formulario
 */
abstract class BaseForm extends Form
{
    /**
     * Modelo asociado al formulario (null para creación).
     */
    public ?Model $model = null;

    /**
     * Establece el modelo para edición y carga sus datos en el formulario.
     *
     * @param  Model  $model  Modelo a editar
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
        $this->fill($model->toArray());
    }

    /**
     * Crea un nuevo registro en la base de datos.
     *
     * Flujo:
     * 1. Autorización (Policy)
     * 2. beforeValidation hook
     * 3. Normalización de datos
     * 4. Validación
     * 5. beforeSave hook
     * 6. Sanitización
     * 7. Creación del modelo
     * 8. afterModelCreated hook
     * 9. afterSave hook
     *
     * @throws AuthorizationException Si el usuario no tiene permiso
     * @throws ValidationException Si los datos no son válidos
     * @throws \Exception Si ocurre un error durante la creación
     */
    public function store(): void
    {
        // 1. Autorización
        $this->authorize('create');

        // 2. Hook pre-validación
        $this->beforeValidation();

        // 3. Normalización y validación
        $this->normalizeData();
        $this->validate();

        // 4. Hook pre-guardado
        $this->beforeSave();

        try {
            DB::beginTransaction();

            // 5. Sanitización y creación
            $data = $this->sanitize($this->getData());
            $model = $this->modelClass()::create($data);

            // 6. Hooks post-creación
            $this->afterModelCreated($model);
            $this->afterSave($model);

            DB::commit();

            Log::info('Registro creado exitosamente', [
                'model' => $this->modelClass(),
                'id' => $model->id,
                'user_id' => Auth::id(),
            ]);

            $this->reset();
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e; // Re-lanzar sin logging (ya se muestra al usuario)
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear registro', [
                'model' => $this->modelClass(),
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }

    }

    /**
     * Actualiza un registro existente en la base de datos.
     *
     * Flujo similar a store() pero con validación de modelo existente.
     *
     * @throws AuthorizationException Si el usuario no tiene permiso
     * @throws ModelNotFoundException Si el modelo no existe
     * @throws ValidationException Si los datos no son válidos
     * @throws \Exception Si ocurre un error durante la actualización
     */
    public function update(): void
    {
        // Validar que existe un modelo
        if (! $this->model) {
            throw new ModelNotFoundException('No se ha establecido un modelo para actualizar.');
        }

        // 1. Autorización
        $this->authorize('update', $this->model);

        // 2. Hook pre-validación
        $this->beforeValidation();

        // 3. Normalización y validación
        $this->normalizeData();
        $this->validate();

        // 4. Hook pre-guardado
        $this->beforeSave();

        try {
            DB::beginTransaction();

            // 5. Sanitización y actualización
            $data = $this->sanitize($this->getData());
            $this->model->update($data);

            // 6. Hooks post-actualización
            $this->afterModelUpdated($this->model);
            $this->afterSave($this->model);

            DB::commit();

            Log::info('Registro actualizado exitosamente', [
                'model' => $this->modelClass(),
                'id' => $this->model->id,
                'user_id' => Auth::id(),
            ]);

            $this->reset();
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al actualizar registro', [
                'model' => $this->modelClass(),
                'id' => $this->model->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    /**
     * Verifica autorización usando Policies.
     *
     * @param  string  $ability  Habilidad a verificar (create, update, delete, etc.)
     * @param  Model|null  $model  Modelo opcional para verificación
     *
     * @throws AuthorizationException Si no está autorizado
     */
    protected function authorize(string $ability, ?Model $model = null): void
    {
        $modelClass = $this->modelClass();

        if ($model) {
            Gate::authorize($ability, $model);
        } else {
            Gate::authorize($ability, $modelClass);
        }
    }

    /**
     * Sanitiza los datos para prevenir XSS y otros ataques.
     *
     * Limpia strings HTML, trim de espacios, y normalización.
     *
     * @param  array<string, mixed>  $data  Datos a sanitizar
     * @return array<string, mixed> Datos sanitizados
     */
    protected function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Trim de espacios
                $value = trim($value);

                // Sanitizar HTML (permitir solo tags seguros si es necesario)
                // Por defecto, strip_tags elimina todo HTML
                $data[$key] = strip_tags($value);
            }
        }

        return $data;
    }

    /**
     * Obtiene los datos que se guardarán en la base de datos.
     *
     * Las clases hijas pueden sobrescribir este método para:
     * - Excluir campos específicos
     * - Transformar datos antes de guardar
     * - Agregar campos calculados
     *
     * @return array<string, mixed> Datos a guardar
     */
    protected function getData(): array
    {
        return $this->pull();
    }

    /**
     * Retorna la clase del modelo asociado.
     *
     * Las clases hijas DEBEN implementar este método.
     *
     * @return class-string<Model> Nombre completo de la clase del modelo
     */
    abstract public function modelClass(): string;

    /**
     * Reglas de validación del formulario.
     *
     * Las clases hijas deben sobrescribir este método con sus reglas específicas.
     *
     * @return array<string, mixed> Reglas de validación de Laravel
     */
    protected function rules(): array
    {
        return [];
    }

    // ==================== HOOKS DE CICLO DE VIDA ====================

    /**
     * Hook: Se ejecuta ANTES de la validación.
     *
     * Útil para:
     * - Preparar datos antes de validar
     * - Cargar relaciones necesarias
     * - Validaciones previas
     */
    protected function beforeValidation(): void
    {
        // Las clases hijas pueden sobrescribir
    }

    /**
     * Hook: Normaliza los datos antes de validar.
     *
     * Útil para:
     * - Convertir a mayúsculas/minúsculas
     * - Formatear fechas
     * - Limpiar espacios extras
     */
    protected function normalizeData(): void
    {
        // Las clases hijas pueden sobrescribir
    }

    /**
     * Hook: Se ejecuta ANTES de guardar (después de validar).
     *
     * Útil para:
     * - Validaciones de negocio complejas
     * - Preparar datos calculados
     * - Verificar condiciones especiales
     */
    protected function beforeSave(): void
    {
        // Las clases hijas pueden sobrescribir
    }

    /**
     * Hook: Se ejecuta DESPUÉS de crear el modelo (dentro de la transacción).
     *
     * Útil para:
     * - Crear registros relacionados
     * - Asignar roles/permisos
     * - Enviar notificaciones
     *
     * @param  Model  $model  Modelo recién creado
     */
    protected function afterModelCreated(Model $model): void
    {
        // Las clases hijas pueden sobrescribir
    }

    /**
     * Hook: Se ejecuta DESPUÉS de actualizar el modelo (dentro de la transacción).
     *
     * Útil para:
     * - Actualizar registros relacionados
     * - Sincronizar datos
     * - Registrar cambios en auditoría
     *
     * @param  Model  $model  Modelo actualizado
     */
    protected function afterModelUpdated(Model $model): void
    {
        // Las clases hijas pueden sobrescribir
    }

    /**
     * Hook: Se ejecuta DESPUÉS de guardar (crear o actualizar).
     *
     * Útil para:
     * - Limpiar cachés
     * - Disparar eventos
     * - Tareas post-guardado comunes
     *
     * @param  Model  $model  Modelo guardado
     */
    protected function afterSave(Model $model): void
    {
        // Las clases hijas pueden sobrescribir
    }
}
