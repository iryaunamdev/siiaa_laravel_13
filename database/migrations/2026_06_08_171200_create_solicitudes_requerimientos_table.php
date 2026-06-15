<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea los requerimientos asociados al expediente principal de solicitud.
     *
     * Aunque por ahora los requerimientos aplican funcionalmente a solicitudes
     * de visitantes, se ligan directamente a solicitudes para mantener el diseño
     * institucional aprobado: los requerimientos pertenecen al expediente, no
     * al registro específico del visitante.
     *
     * Los requerimientos se seleccionan desde catálogo y conservan auditoría
     * institucional mediante identity_links.id.
     */
    public function up(): void
    {
        Schema::create('solicitudes_requerimientos', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Solicitud relacionada
            |--------------------------------------------------------------------------
            |
            | El requerimiento pertenece al expediente principal de solicitud.
            | Si la solicitud se elimina, también se eliminan sus requerimientos.
            |
            */
            $table->foreignId('solicitud_id')
                ->constrained('solicitudes')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Requerimiento
            |--------------------------------------------------------------------------
            |
            | requerimiento_id apunta a catalogos_items.
            | Para visitantes, el catálogo esperado es VIS_REQ.
            |
            */
            $table->foreignId('requerimiento_id')
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Auditoría institucional
            |--------------------------------------------------------------------------
            |
            | Se usa identity_links.id, no users.id, para conservar trazabilidad
            | institucional consistente con el módulo.
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
            | Índices y restricciones
            |--------------------------------------------------------------------------
            */
            $table->unique(
                ['solicitud_id', 'requerimiento_id'],
                'sol_req_solicitud_requerimiento_unique'
            );

            $table->index('solicitud_id');
            $table->index('requerimiento_id');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_requerimientos');
    }
};