<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Agregar índices para optimización de performance
 *
 * Agrega índices estratégicos en columnas frecuentemente consultadas
 * para mejorar el rendimiento de queries de búsqueda, filtrado y joins.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Offices
        try {
            Schema::table('offices', function (Blueprint $table) {
                $table->index(['is_active', 'code'], 'offices_active_code_idx');
                $table->index('name', 'offices_name_idx');
            });
        } catch (\Exception $e) {
            // Índices ya existen
        }

        // Users
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['is_active', 'email'], 'users_active_email_idx');
                $table->index('name', 'users_name_idx');
            });
        } catch (\Exception $e) {
            // Índices ya existen
        }

        // Subunits
        try {
            Schema::table('subunits', function (Blueprint $table) {
                $table->index(['office_id', 'is_system'], 'subunits_office_system_idx');
                $table->index(['office_id', 'is_active'], 'subunits_office_active_idx');
                $table->index('name', 'subunits_name_idx');
            });
        } catch (\Exception $e) {
            // Índices ya existen
        }

        // Catalog tables
        $catalogTables = ['purposes', 'financings', 'classifiers', 'activities', 'products', 'budget_programs', 'goals', 'documents'];

        foreach ($catalogTables as $tableName) {
            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->index('is_active', "{$tableName}_active_idx");
                    $table->index('name', "{$tableName}_name_idx");
                });
            } catch (\Exception $e) {
                // Índices ya existen
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropIndex('offices_active_code_idx');
            $table->dropIndex('offices_name_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_active_email_idx');
            $table->dropIndex('users_name_idx');
        });

        Schema::table('subunits', function (Blueprint $table) {
            $table->dropIndex('subunits_office_system_idx');
            $table->dropIndex('subunits_office_active_idx');
            $table->dropIndex('subunits_name_idx');
        });

        $catalogTables = ['purposes', 'financings', 'classifiers', 'activities', 'products', 'budget_programs', 'goals', 'documents'];

        foreach ($catalogTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropIndex("{$tableName}_active_idx");
                $table->dropIndex("{$tableName}_name_idx");
            });
        }
    }
};
