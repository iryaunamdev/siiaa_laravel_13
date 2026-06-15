<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de recursos asociados a solicitudes.
     *
     * Una solicitud puede tener uno o más registros de recursos.
     * La política de viáticos/recursos se acepta a nivel solicitud,
     * no por cada recurso individual.
     */
    public function up(): void
    {
        Schema::create('solicitudes_recursos', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Solicitud relacionada
            |--------------------------------------------------------------------------
            */
            $table->foreignId('solicitud_id')
                ->constrained('solicitudes')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Origen del recurso
            |--------------------------------------------------------------------------
            |
            | origen_id apunta al catálogo C_OREC:
            | R_PI, R_PAPIIT, R_PAPIME, CONV, R_IRYA, SECIHT.
            |
            */
            $table->foreignId('origen_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Proyecto
            |--------------------------------------------------------------------------
            |
            | proyecto_id queda disponible para un futuro módulo de proyectos.
            | proyecto_nombre permite registrar el nombre libremente en esta etapa.
            |
            */
            $table->unsignedBigInteger('proyecto_id')->nullable();
            $table->string('proyecto_nombre')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Días / periodos de apoyo
            |--------------------------------------------------------------------------
            |
            | Se conservan los campos cercanos al diseño original del SIIAA_10.
            |
            */
            $table->unsignedSmallInteger('dias_n')->nullable();
            $table->unsignedSmallInteger('dias_i')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Montos solicitados
            |--------------------------------------------------------------------------
            |
            | Los montos usan decimal(12,2).
            | Las divisas apuntan al catálogo DIVISAS: MXN, USD, EUR.
            |
            */
            $table->decimal('cuota', 12, 2)->nullable();

            $table->foreignId('cuota_divisa')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->decimal('avion', 12, 2)->nullable();

            $table->foreignId('avion_divisa')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->decimal('otro', 12, 2)->nullable();

            $table->foreignId('otro_divisa')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Información adicional del recurso
            |--------------------------------------------------------------------------
            |
            | Campo amplio para notas operativas, detalle del concepto "otro",
            | o aclaraciones como uso de remanentes de partida.
            |
            */
            $table->text('informacion_adicional')->nullable();

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
            $table->index('solicitud_id');
            $table->index('origen_id');
            $table->index('proyecto_id');

            $table->index('cuota_divisa');
            $table->index('avion_divisa');
            $table->index('otro_divisa');

            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_recursos');
    }
};