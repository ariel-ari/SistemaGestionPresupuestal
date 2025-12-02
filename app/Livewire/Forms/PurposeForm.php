<?php

namespace App\Livewire\Forms;

use App\Models\Purpose;
use Illuminate\Validation\Rule;

/**
 * Form para gestión de Purposes (Finalidades Presupuestales).
 *
 * Maneja la creación y edición de finalidades con:
 * - Validación de unicidad case-insensitive
 * - Normalización automática de nombre
 * - Soporte para descripción opcional
 */
class PurposeForm extends BaseForm
{
    /**
     * Nombre de la finalidad presupuestal.
     */
    public ?string $name = '';

    /**
     * Descripción opcional de la finalidad.
     */
    // public ?string $description = '';

    /**
     * Estado activo/inactivo.
     */
    public bool $is_active = true;

    /**
     * Retorna la clase del modelo asociado.
     */
    public function modelClass(): string
    {
        return Purpose::class;
    }

    /**
     * Reglas de validación del formulario.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = $this->model?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('purposes', 'name')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            //'description' => [
            //    'nullable',
            //    'string',
            //    'max:500',
            //],
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
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'name.unique' => 'Este nombre ya está registrado.',

            //'description.max' => 'La descripción no puede superar los 500 caracteres.',
        ];
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
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return $this->only(['name', 'is_active']);
    }
}
