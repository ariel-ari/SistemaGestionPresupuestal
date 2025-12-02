<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Form para gestión de Products (Productos Presupuestales). */
class ProductForm extends BaseForm
{
    public ?string $code = '';

    public ?string $name = '';

    public ?string $description = '';

    public bool $is_active = true;

    public function modelClass(): string
    {
        return Product::class;
    }

    public function rules(): array
    {
        $id = $this->model?->id;

        return [
            'code' => ['required', 'string', 'min:1', 'max:10', 'regex:/^[A-Z0-9-]+$/', Rule::unique('products', 'code')->ignore($id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('products', 'name')->ignore($id)->whereNull('deleted_at')],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código es obligatorio.',
            'code.regex' => 'El código solo puede contener letras mayúsculas, números y guiones.',
            'code.unique' => 'Este código ya está registrado.',
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.unique' => 'Este nombre ya está registrado.',
        ];
    }

    protected function normalizeData(): void
    {
        $this->code = Str::upper(trim(preg_replace('/\s+/', ' ', $this->code ?? '')));
        $this->name = mb_convert_case(trim(preg_replace('/\s+/', ' ', $this->name ?? '')), MB_CASE_TITLE, 'UTF-8');
        if ($this->description) {
            $this->description = trim(preg_replace('/\s+/', ' ', $this->description));
        }
    }

    protected function getData(): array
    {
        return $this->only(['code', 'name', 'description', 'is_active']);
    }
}
