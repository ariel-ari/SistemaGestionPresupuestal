<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regla de validación para montos presupuestales.
 *
 * Valida que el monto sea un número positivo y no exceda un límite.
 */
class BudgetAmount implements ValidationRule
{
    protected float $maxAmount;

    /**
     * Create a new rule instance.
     *
     * @param  float  $maxAmount  Monto máximo permitido
     */
    public function __construct(float $maxAmount = 999999999.99)
    {
        $this->maxAmount = $maxAmount;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value)) {
            $fail('El :attribute debe ser un número válido.');

            return;
        }

        $amount = (float) $value;

        if ($amount < 0) {
            $fail('El :attribute debe ser un valor positivo.');

            return;
        }

        if ($amount > $this->maxAmount) {
            $fail('El :attribute no puede exceder '.format_currency($this->maxAmount).'.');

            return;
        }

        // Validar que tenga máximo 2 decimales
        if (round($amount, 2) != $amount) {
            $fail('El :attribute solo puede tener hasta 2 decimales.');

            return;
        }
    }
}
