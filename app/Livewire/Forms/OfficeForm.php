<?php

namespace App\Livewire\Forms;

use App\Models\Office;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Form para gestión de Offices (Centros de Costo).
 *
 * Maneja la creación y edición de centros de costo con:
 * - Validación de unicidad case-insensitive
 * - Normalización automática de código y nombre
 * - Validación de formato de código
 */
class OfficeForm extends BaseForm
{
    /**
     * Código único del centro de costo.
     */
    public ?string $code = '';

    /**
     * Nombre del centro de costo.
     */
    public ?string $name = '';

    /**
     * Descripción opcional del centro de costo.
     */
    public ?string $description = '';

    /**
     * Estado activo/inactivo.
     */
    public bool $is_active = true;

    /**
     * Retorna la clase del modelo asociado.
     */
    public function modelClass(): string
    {
        return Office::class;
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
            'code' => [
                'required',
                'string',
                'min:2',
                'max:20',
                'regex:/^[A-Z0-9-]+$/', // Solo mayúsculas, números y guiones
                Rule::unique('offices', 'code')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                Rule::unique('offices', 'name')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
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
            'code.required' => 'El código es obligatorio.',
            'code.min' => 'El código debe tener al menos 2 caracteres.',
            'code.max' => 'El código no puede superar los 20 caracteres.',
            'code.regex' => 'El código solo puede contener letras mayúsculas, números y guiones.',
            'code.unique' => 'Este código ya está registrado.',

            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'name.unique' => 'Este nombre ya está registrado.',

            'description.max' => 'La descripción no puede superar los 500 caracteres.',
        ];
    }

    /**
     * Normaliza los datos antes de validar.
     *
     * - Código: MAYÚSCULAS, trim, espacios únicos
     * - Nombre: Title Case, trim, espacios únicos
     * - Descripción: trim, espacios únicos
     */
    protected function normalizeData(): void
    {
        // Normalizar código: MAYÚSCULAS y limpiar espacios
        $this->code = Str::upper(trim(preg_replace('/\s+/', ' ', $this->code ?? '')));

        // Normalizar nombre: Title Case y limpiar espacios
        $this->name = mb_convert_case(
            trim(preg_replace('/\s+/', ' ', $this->name ?? '')),
            MB_CASE_TITLE,
            'UTF-8'
        );

        // Normalizar descripción: limpiar espacios
        if ($this->description) {
            $this->description = trim(preg_replace('/\s+/', ' ', $this->description));
        }
    }

    /**
     * Obtiene los datos para guardar, excluyendo campos no necesarios.
     *
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return $this->only(['code', 'name', 'description', 'is_active']);
    }
}
