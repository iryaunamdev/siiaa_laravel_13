<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de ingresos/periodos de estudiantes asociados.
     *
     * El tutor institucional se resuelve mediante identity_links.id,
     * no mediante personas.id ni users.id.
     */
    public function up(): void
    {
        Schema::create('estudiantes_asociados_ingresos', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Estudiante asociado
            |--------------------------------------------------------------------------
            */
            $table->foreignId('estudiante_id')
                ->constrained('estudiantes_asociados')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Tutor institucional
            |--------------------------------------------------------------------------
            |
            | tutor_id apunta a identity_links.id.
            |
            */
            $table->foreignId('tutor_id')
                ->nullable()
                ->constrained('identity_links')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Catálogos
            |--------------------------------------------------------------------------
            */
            $table->foreignId('grado_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->foreignId('tipo_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->foreignId('universidad_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Periodo
            |--------------------------------------------------------------------------
            */
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Auditoría institucional
            |--------------------------------------------------------------------------
            |
            | created_by y updated_by apuntan a identity_links.id.
            |
            */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */
            $table->index('estudiante_id');
            $table->index('tutor_id');
            $table->index('grado_id');
            $table->index('tipo_id');
            $table->index('universidad_id');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes_asociados_ingresos');
    }
};