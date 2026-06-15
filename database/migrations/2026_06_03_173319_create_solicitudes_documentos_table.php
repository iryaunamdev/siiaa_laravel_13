<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de documentos adjuntos a solicitudes.
     *
     * Los documentos se manejan de forma flexible:
     * - No bloquean globalmente el envío.
     * - No requieren campos adicionales visibles para el usuario.
     * - El sistema guarda metadatos automáticamente.
     */
    public function up(): void
    {
        Schema::create('solicitudes_documentos', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Solicitud relacionada
            |--------------------------------------------------------------------------
            |
            | Al borrar una solicitud, se eliminan sus documentos registrados.
            | El archivo físico debe eliminarse desde la lógica de servicio/modelo,
            | no solo con la restricción de base de datos.
            |
            */
            $table->foreignId('solicitud_id')
                ->constrained('solicitudes')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Archivo
            |--------------------------------------------------------------------------
            |
            | filename: nombre interno almacenado.
            | original_name: nombre original del archivo subido.
            | path: ruta relativa en storage.
            |
            | Ruta aprobada:
            | documentos/solicitudes/{year}/{solicitud_id}/
            |
            */
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');

            /*
            |--------------------------------------------------------------------------
            | Metadatos
            |--------------------------------------------------------------------------
            */
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Autoría de carga
            |--------------------------------------------------------------------------
            |
            | uploaded_by apunta a identity_links.id.
            |
            */
            $table->foreignId('uploaded_by')
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
            $table->index('uploaded_by');
            $table->index('mime_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_documentos');
    }
};