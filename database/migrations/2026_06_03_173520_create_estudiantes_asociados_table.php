<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de estudiantes asociados.
     *
     * Los estudiantes asociados no son estudiantes SIIAP adscritos al IRyA;
     * se usan principalmente para solicitudes de visitantes tipo estudiante asociado.
     */
    public function up(): void
    {
        Schema::create('estudiantes_asociados', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Datos generales
            |--------------------------------------------------------------------------
            */
            $table->string('nombre');
            $table->string('apellidop');
            $table->string('apellidom')->nullable();

            $table->string('email')->nullable();

            $table->string('curp')->nullable()->unique();
            $table->string('rfc')->nullable()->unique();

            $table->date('fecha_nacimiento')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Catálogos
            |--------------------------------------------------------------------------
            */
            $table->foreignId('sexo_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->foreignId('nacionalidad_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Perfil
            |--------------------------------------------------------------------------
            */
            $table->string('photo_url', 2480)->nullable();

            /*
             * Usamos activo, no active, para mantener consistencia con el modelo
             * y con los scopes ya existentes.
             */
            $table->boolean('activo')->default(true);

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
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */
            $table->index('email');
            $table->index('activo');
            $table->index('sexo_id');
            $table->index('nacionalidad_id');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes_asociados');
    }
};