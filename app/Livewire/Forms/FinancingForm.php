<?php

namespace App\Livewire\Forms;

use App\Models\Financing;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Form para gestión de Financings (Fuentes de Financiamiento).
 *
 * Maneja la creación y edición de fuentes de financiamiento con:
 * - Validación de código y nombre únicos
 * - Normalización automática
 * - Validación de formato de código
 */
class FinancingForm extends BaseForm
{
    /**
     * Código único de la fuente de financiamiento.
     */
    public ?string $code = '';

    /**
     * Nombre de la fuente de financiamiento.
     */
    public ?string $name = '';

    /**
     * Descripción opcional.
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
        return Financing::class;
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
                'min:1',
                'max:10',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('financings', 'code')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('financings', 'name')
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
            'code.min' => 'El código debe tener al menos 1 carácter.',
            'code.max' => 'El código no puede superar los 10 caracteres.',
            'code.regex' => 'El código solo puede contener letras mayúsculas, números y guiones.',
            'code.unique' => 'Este código ya está registrado.',

            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'name.unique' => 'Este nombre ya está registrado.',

            'description.max' => 'La descripción no puede superar los 500 caracteres.',
        ];
    }

    /**
     * Normaliza los datos antes de validar.
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

        // Normalizar descripción
        if ($this->description) {
            $this->description = trim(preg_replace('/\s+/', ' ', $this->description));
        }
    }

    /**
     * Obtiene los datos para guardar.
     *
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        return $this->only(['code', 'name', 'description', 'is_active']);
    }
}
