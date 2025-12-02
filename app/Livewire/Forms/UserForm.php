<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Form para gestión de Users (Usuarios).
 *
 * Maneja la creación y edición de usuarios con:
 * - Validación de contraseña fuerte
 * - Gestión de roles con Spatie Permission
 * - Validación de email con DNS check
 * - Protección de datos sensibles
 */
class UserForm extends BaseForm
{
    /**
     * Nombre completo del usuario.
     */
    public ?string $name = '';

    /**
     * Correo electrónico único del usuario.
     */
    public ?string $email = '';

    /**
     * ID del rol a asignar al usuario.
     */
    public ?int $role = null;

    /**
     * Contraseña del usuario (solo para creación/cambio).
     */
    public ?string $password = '';

    /**
     * Estado activo/inactivo del usuario.
     */
    public bool $is_active = true;

    /**
     * Retorna la clase del modelo asociado.
     */
    public function modelClass(): string
    {
        return User::class;
    }

    /**
     * Sobrescribe setModel para cargar el rol del usuario.
     *
     * Carga el ID del primer rol del usuario para mostrarlo en el formulario.
     * La contraseña se limpia para no exponerla.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function setModel($model): void
    {
        parent::setModel($model);

        // Cargar el ID del primer rol del usuario
        $this->role = $model->roles->first()?->id;

        // Limpiar contraseña para no exponerla
        $this->password = '';
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
                'min:2',
                'max:100',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns', // Validar formato y DNS
                'max:100',
                Rule::unique('users', 'email')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'role' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'password' => [
                $this->model ? 'nullable' : 'required', // Opcional en edición
                'string',
                Password::min(8)
                    ->letters() // Requiere letras
                    ->mixedCase() // Mayúsculas y minúsculas
                    ->numbers(), // Requiere números, // Verifica en base de datos de contraseñas filtradas
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
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede superar los 100 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'role.required' => 'Debe seleccionar un rol.',
            'role.integer' => 'El rol seleccionado no es válido.',
            'role.exists' => 'El rol seleccionado no existe.',

            'password.required' => 'La contraseña es obligatoria.',
        ];
    }

    /**
     * Normaliza los datos antes de validar.
     *
     * - Nombre: Title Case, trim, espacios únicos
     * - Email: minúsculas, trim
     */
    protected function normalizeData(): void
    {
        // Normalizar nombre: Title Case y limpiar espacios
        $this->name = mb_convert_case(
            trim(preg_replace('/\s+/', ' ', $this->name ?? '')),
            MB_CASE_TITLE,
            'UTF-8'
        );

        // Normalizar email: minúsculas y trim
        $this->email = mb_convert_case(trim($this->email ?? ''), MB_CASE_LOWER, 'UTF-8');
    }

    /**
     * Excluye 'role' y hashea la contraseña antes de guardar.
     *
     * El rol se maneja en afterModelCreated/afterModelUpdated.
     * La contraseña se hashea solo si no está vacía.
     *
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $data = $this->except(['role']);

        // Hashear la contraseña solo si no está vacía
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Si está vacía (en edición), no incluirla
            unset($data['password']);
        }

        return $data;
    }

    /**
     * Hook: Asigna el rol después de crear el usuario.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    protected function afterModelCreated($model): void
    {
        if ($this->role) {
            $model->syncRoles($this->role);
        }
    }

    /**
     * Hook: Actualiza el rol después de actualizar el usuario.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    protected function afterModelUpdated($model): void
    {
        if ($this->role) {
            $model->syncRoles($this->role);
        }
    }
}
