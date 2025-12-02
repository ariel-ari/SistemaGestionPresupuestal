<?php

/**
 * Helpers Globales del Sistema
 *
 * Funciones de utilidad disponibles en toda la aplicación.
 */
if (! function_exists('format_currency')) {
    /**
     * Formatea un número como moneda.
     *
     * @param  float|int  $amount  Cantidad a formatear
     * @param  string  $currency  Símbolo de moneda
     * @return string Cantidad formateada
     */
    function format_currency(float|int $amount, string $currency = 'Q'): string
    {
        return $currency.' '.number_format($amount, 2, '.', ',');
    }
}

if (! function_exists('format_percentage')) {
    /**
     * Formatea un número como porcentaje.
     *
     * @param  float|int  $value  Valor a formatear
     * @param  int  $decimals  Número de decimales
     * @return string Porcentaje formateado
     */
    function format_percentage(float|int $value, int $decimals = 2): string
    {
        return number_format($value, $decimals, '.', ',').'%';
    }
}

if (! function_exists('sanitize_filename')) {
    /**
     * Sanitiza un nombre de archivo eliminando caracteres peligrosos.
     *
     * @param  string  $filename  Nombre de archivo
     * @return string Nombre sanitizado
     */
    function sanitize_filename(string $filename): string
    {
        // Eliminar caracteres peligrosos
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Eliminar múltiples guiones bajos consecutivos
        $filename = preg_replace('/_+/', '_', $filename);

        return trim($filename, '_');
    }
}

if (! function_exists('generate_code')) {
    /**
     * Genera un código único basado en un prefijo.
     *
     * @param  string  $prefix  Prefijo del código
     * @param  int  $length  Longitud del código numérico
     * @return string Código generado
     */
    function generate_code(string $prefix, int $length = 6): string
    {
        $number = str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);

        return strtoupper($prefix).'-'.$number;
    }
}

if (! function_exists('is_super_admin')) {
    /**
     * Verifica si el usuario actual es Super Admin.
     */
    function is_super_admin(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Super Admin');
    }
}

if (! function_exists('can_access')) {
    /**
     * Verifica si el usuario actual tiene un permiso específico.
     *
     * @param  string  $permission  Nombre del permiso
     */
    function can_access(string $permission): bool
    {
        return auth()->check() && auth()->user()->can($permission);
    }
}

if (! function_exists('active_users_count')) {
    /**
     * Obtiene el conteo de usuarios activos.
     */
    function active_users_count(): int
    {
        return \App\Models\User::where('is_active', true)->count();
    }
}

if (! function_exists('log_activity')) {
    /**
     * Registra una actividad del usuario en los logs.
     *
     * @param  string  $action  Acción realizada
     * @param  string  $model  Modelo afectado
     * @param  int|null  $modelId  ID del modelo
     * @param  array  $extra  Datos adicionales
     */
    function log_activity(string $action, string $model, ?int $modelId = null, array $extra = []): void
    {
        \Illuminate\Support\Facades\Log::info("User activity: {$action}", [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'extra' => $extra,
        ]);
    }
}

if (! function_exists('fiscal_year')) {
    /**
     * Obtiene el año fiscal actual.
     */
    function fiscal_year(): int
    {
        return (int) now()->format('Y');
    }
}

if (! function_exists('truncate_text')) {
    /**
     * Trunca un texto a una longitud específica.
     *
     * @param  string  $text  Texto a truncar
     * @param  int  $length  Longitud máxima
     * @param  string  $suffix  Sufijo a agregar
     * @return string Texto truncado
     */
    function truncate_text(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length).$suffix;
    }
}

if (! function_exists('flash_success')) {
    /**
     * Agrega un mensaje de éxito a la sesión.
     *
     * @param  string  $message  Mensaje
     */
    function flash_success(string $message): void
    {
        session()->flash('success', $message);
    }
}

if (! function_exists('flash_error')) {
    /**
     * Agrega un mensaje de error a la sesión.
     *
     * @param  string  $message  Mensaje
     */
    function flash_error(string $message): void
    {
        session()->flash('error', $message);
    }
}

if (! function_exists('flash_warning')) {
    /**
     * Agrega un mensaje de advertencia a la sesión.
     *
     * @param  string  $message  Mensaje
     */
    function flash_warning(string $message): void
    {
        session()->flash('warning', $message);
    }
}

if (! function_exists('flash_info')) {
    /**
     * Agrega un mensaje informativo a la sesión.
     *
     * @param  string  $message  Mensaje
     */
    function flash_info(string $message): void
    {
        session()->flash('info', $message);
    }
}
