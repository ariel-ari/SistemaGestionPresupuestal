<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regla de validación para contraseñas fuertes.
 *
 * Valida que la contraseña cumpla con requisitos de seguridad:
 * - Mínimo 8 caracteres
 * - Al menos una letra mayúscula
 * - Al menos una letra minúscula
 * - Al menos un número
 */
class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 8) {
            $fail('La :attribute debe tener al menos 8 caracteres.');

            return;
        }

        if (! preg_match('/[a-z]/', $value)) {
            $fail('La :attribute debe contener al menos una letra minúscula.');

            return;
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('La :attribute debe contener al menos una letra mayúscula.');

            return;
        }

        if (! preg_match('/[0-9]/', $value)) {
            $fail('La :attribute debe contener al menos un número.');

            return;
        }
    }
}
