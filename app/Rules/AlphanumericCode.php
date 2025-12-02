<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regla de validación para códigos alfanuméricos.
 *
 * Valida que el código solo contenga letras mayúsculas, números y guiones.
 * Útil para códigos de catálogos (offices, financings, etc.)
 */
class AlphanumericCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^[A-Z0-9-]+$/', $value)) {
            $fail('El :attribute solo puede contener letras mayúsculas, números y guiones.');
        }
    }
}
