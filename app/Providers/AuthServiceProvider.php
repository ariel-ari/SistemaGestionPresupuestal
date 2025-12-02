<?php

namespace App\Providers;

use App\Models\Office;
use App\Models\Subunit;
use App\Models\User;
use App\Policies\OfficePolicy;
use App\Policies\SubunitPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Service Provider para configuración de autorización y policies.
 *
 * Registra las policies del sistema y configura gates globales
 * para control de acceso granular.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de modelos a sus policies correspondientes.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Office::class => OfficePolicy::class,
        User::class => UserPolicy::class,
        Subunit::class => SubunitPolicy::class,
    ];

    /**
     * Registra servicios de autenticación/autorización.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa servicios de autenticación/autorización.
     *
     * Registra policies y gates personalizados.
     */
    public function boot(): void
    {
        // Registrar policies automáticamente
        $this->registerPolicies();

        // Gate global: Super Admin puede hacer todo
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('Super Admin')) {
                return true;
            }
        });

        // Gate personalizado: verificar si usuario está activo
        Gate::define('is-active', function (User $user) {
            return $user->is_active === true;
        });

        // Gate personalizado: verificar si puede gestionar roles
        Gate::define('manage-roles', function (User $user) {
            return $user->hasPermissionTo('edit users');
        });
    }
}
