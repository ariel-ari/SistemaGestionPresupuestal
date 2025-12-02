<?php

namespace App\Livewire\Forms;

use App\Models\Subunit;
use Illuminate\Validation\Rule;

/**
 * Form para gestión de Subunits (Finalidades).
 *
 * Maneja la creación y edición de subunidades/finalidades con:
 * - Validación de unicidad por centro de costo
 * - Protección de subunidades del sistema
 * - Normalización automática de nombre
 */
class SubunitForm extends BaseForm
{
    /**
     * ID del centro de costo al que pertenece.
     */
    public ?int $office_id = null;

    /**
     * Nombre de la finalidad/subunidad.
     */
    public ?string $name = '';

    /**
     * Descripción opcional de la finalidad.
     */
    //public ?string $description = '';

    /**
     * Estado activo/inactivo.
     */
    public bool $is_active = true;

    /**
     * Retorna la clase del modelo asociado.
     */
    public function modelClass(): string
    {
        return Subunit::class;
    }

    /**
     * Reglas de validación del formulario.
     *
     * El nombre debe ser único dentro del mismo centro de costo.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->model?->id;

        return [
            'office_id' => [
                'required',
                'integer',
                'exists:offices,id',
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                // Único dentro del mismo office
                Rule::unique('subunits', 'name')
                    ->ignore($id)
                    ->where('office_id', $this->office_id)
                    ->whereNull('deleted_at'),
            ],
            // 'description' => [
            //     'nullable',
            //     'string',
            //     'max:500',
            // ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    /**
     * Mensajes de validación personalizados.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'office_id.required' => 'El centro de costo es obligatorio.',
            'office_id.exists' => 'El centro de costo seleccionado no existe.',

            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.unique' => 'Ya existe una finalidad con este nombre en este centro de costo.',

            // 'description.max' => 'La descripción no puede superar los 500 caracteres.',
        ];
    }

    /**
     * Hook: Valida que no sea una subunidad del sistema antes de guardar.
     *
     * Las subunidades del sistema (is_system = true) no pueden ser editadas
     * manualmente ya que se sincronizan automáticamente con el Office.
     *
     * @throws \Exception Si se intenta editar una subunidad del sistema
     */
    protected function beforeSave(): void
    {
        if ($this->model && $this->model->is_system) {
            throw new \Exception(
                'No puedes modificar esta finalidad porque es una finalidad principal del sistema. '.
                'Esta se sincroniza automáticamente con el centro de costo.'
            );
        }
    }

    /**
     * Normaliza los datos antes de validar.
     *
     * - Nombre: Title Case, trim, espacios únicos
     * - Descripción: trim, espacios únicos
     */
    protected function normalizeData(): void
    {
        // Normalizar nombre: Title Case y limpiar espacios
        $this->name = mb_convert_case(
            trim(preg_replace('/\s+/', ' ', $this->name ?? '')),
            MB_CASE_TITLE,
            'UTF-8'
        );

        // Normalizar descripción: limpiar espacios
       // if ($this->description) {
       //     $this->description = trim(preg_replace('/\s+/', ' ', $this->description));
       // }
    }

    /**
     * Obtiene los datos para guardar.
     *
     * Asegura que las subunidades creadas manualmente tengan is_system = false.
     *
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $data = $this->only(['office_id', 'name', 'is_active']);

        // Las subunidades creadas manualmente nunca son del sistema
        if (! $this->model) {
            $data['is_system'] = false;
        }

        return $data;
    }
}
