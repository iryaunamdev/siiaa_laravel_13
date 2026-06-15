<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla principal del módulo de solicitudes.
     *
     * En esta tabla viven los datos comunes a todos los tipos de solicitud.
     * Los datos específicos de visitantes, recursos, documentos y requerimientos
     * se almacenan en tablas hijas.
     */
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Folio institucional
            |--------------------------------------------------------------------------
            |
            | El folio se asigna hasta el envío formal de la solicitud.
            | Mientras la solicitud esté en borrador, estos campos permanecen nulos.
            |
            */
            $table->string('folio', 20)->nullable();
            $table->unsignedSmallInteger('folio_year')->nullable();
            $table->unsignedInteger('folio_number')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Identidad institucional
            |--------------------------------------------------------------------------
            |
            | Todas las referencias de propiedad y auditoría apuntan a identity_links.id.
            |
            */
            $table->foreignId('owner_id')
                ->constrained('identity_links')
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Clasificación de la solicitud
            |--------------------------------------------------------------------------
            */
            $table->foreignId('tipo_solicitud_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->foreignId('motivo_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->text('motivo_otro')->nullable();

            $table->boolean('requiere_recursos')->default(false);

            /*
            |--------------------------------------------------------------------------
            | Datos generales
            |--------------------------------------------------------------------------
            */
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            $table->foreignId('pais_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->string('nombre_evento')->nullable();
            $table->string('tipo_presentacion')->nullable();

            /*
             * Para solicitudes generales se usa institución libre.
             * Visitantes usan institución flexible en solicitudes_visitantes.
             */
            $table->string('institucion')->nullable();

            $table->string('anfitrion')->nullable();
            $table->string('lugar')->nullable();

            /*
             * Tutor institucional.
             * Debe apuntar a identity_links.id.
             */
            $table->foreignId('tutor_id')
                ->nullable()
                ->constrained('identity_links')
                ->restrictOnDelete();

            $table->text('informacion_adicional')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Seguro UNAM
            |--------------------------------------------------------------------------
            */
            $table->boolean('requiere_seguro_unam')->default(false);
            $table->text('seguro_unam_beneficiario')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Observaciones operativas
            |--------------------------------------------------------------------------
            |
            | No se crea tabla de observaciones en esta etapa.
            | SACAD y Administración editan estos campos según permisos.
            |
            */
            $table->text('observaciones_sacad')->nullable();
            $table->text('observaciones_administracion')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado y fechas de flujo
            |--------------------------------------------------------------------------
            */
            $table->foreignId('estatus_id')
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Política de recursos / viáticos
            |--------------------------------------------------------------------------
            |
            | Se acepta una sola vez por solicitud, aunque existan varios recursos.
            |
            */
            $table->timestamp('politica_aceptada_at')->nullable();

            $table->foreignId('politica_aceptada_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->string('politica_version')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Archivo lógico
            |--------------------------------------------------------------------------
            |
            | No se usan soft deletes. El histórico se maneja con archivo lógico.
            |
            */
            $table->timestamp('archived_at')->nullable();

            $table->foreignId('archived_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->text('archive_reason')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */
            $table->unique(['folio_year', 'folio_number']);
            $table->index('folio');

            $table->index('owner_id');
            $table->index('created_by');
            $table->index('updated_by');

            $table->index('tipo_solicitud_id');
            $table->index('motivo_id');
            $table->index('estatus_id');
            $table->index('pais_id');
            $table->index('tutor_id');

            $table->index('folio_year');
            $table->index('submitted_at');
            $table->index('submitted_by');
            $table->index('approved_by');
            $table->index('rejected_by');
            $table->index('closed_by');
            $table->index('cancelled_by');
            $table->index('archived_at');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
