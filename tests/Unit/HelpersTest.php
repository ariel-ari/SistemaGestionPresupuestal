<?php

use function Pest\Laravel\actingAs;

/**
 * Tests para Helper Functions
 *
 * Estos son tests SIMPLES para empezar a entender cómo funcionan los tests.
 * Cada test verifica una función helper específica.
 */

// ==================== TESTS DE FORMATO ====================

test('format_currency formatea montos correctamente', function () {
    // Arrange: Preparar el dato
    $amount = 1500.50;

    // Act: Ejecutar la función
    $result = format_currency($amount);

    // Assert: Verificar el resultado
    expect($result)->toBe('Q 1,500.50');
});

test('format_currency maneja números enteros', function () {
    $result = format_currency(1000);
    expect($result)->toBe('Q 1,000.00');
});

test('format_currency maneja cero', function () {
    $result = format_currency(0);
    expect($result)->toBe('Q 0.00');
});

test('format_percentage formatea porcentajes correctamente', function () {
    $result = format_percentage(75.5);
    expect($result)->toBe('75.50%');
});

test('truncate_text trunca texto largo', function () {
    $text = 'Este es un texto muy largo que necesita ser truncado';
    $result = truncate_text($text, 20);

    expect($result)->toBe('Este es un texto muy...');
    expect(strlen($result))->toBeLessThanOrEqual(23); // 20 + '...'
});

test('truncate_text no trunca texto corto', function () {
    $text = 'Texto corto';
    $result = truncate_text($text, 20);

    expect($result)->toBe('Texto corto');
});

// ==================== TESTS DE UTILIDADES ====================

test('sanitize_filename elimina caracteres peligrosos', function () {
    $filename = 'archivo con espacios & símbolos!.pdf';
    $result = sanitize_filename($filename);

    expect($result)->toBe('archivo_con_espacios_símbolos_.pdf');
    expect($result)->not->toContain(' ');
    expect($result)->not->toContain('&');
});

test('generate_code genera código con formato correcto', function () {
    $code = generate_code('TEST', 6);

    // Verificar formato: PREFIX-XXXXXX
    expect($code)->toStartWith('TEST-');
    expect(strlen($code))->toBe(11); // TEST- (5) + 6 dígitos
});

test('fiscal_year retorna año actual', function () {
    $year = fiscal_year();
    $currentYear = (int) date('Y');

    expect($year)->toBe($currentYear);
});

// ==================== TESTS DE PERMISOS ====================

test('is_super_admin retorna true para super admin', function () {
    // Crear usuario Super Admin
    $user = \App\Models\User::factory()->create();
    $user->assignRole('Super Admin');

    // Actuar como ese usuario
    actingAs($user);

    // Verificar
    expect(is_super_admin())->toBeTrue();
});

test('is_super_admin retorna false para usuario normal', function () {
    $user = \App\Models\User::factory()->create();
    $user->assignRole('Usuario');

    actingAs($user);

    expect(is_super_admin())->toBeFalse();
});

test('is_super_admin retorna false si no hay usuario autenticado', function () {
    expect(is_super_admin())->toBeFalse();
});

test('can_access verifica permisos correctamente', function () {
    $user = \App\Models\User::factory()->create();
    $user->givePermissionTo('view offices');

    actingAs($user);

    expect(can_access('view offices'))->toBeTrue();
    expect(can_access('delete offices'))->toBeFalse();
});
