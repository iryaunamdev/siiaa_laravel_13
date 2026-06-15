<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de visitantes ligados a solicitudes.
     *
     * Una solicitud de tipo VISITA tendrá un solo visitante principal.
     * Si se requiere registrar más de un visitante, se deberán crear solicitudes separadas.
     */
    public function up(): void
    {
        Schema::create('solicitudes_visitantes', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Solicitud relacionada
            |--------------------------------------------------------------------------
            |
            | Una solicitud visitante tiene un solo registro principal en esta tabla.
            | Al borrar la solicitud, se elimina también su visitante.
            |
            */
            $table->foreignId('solicitud_id')
                ->constrained('solicitudes')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Clasificación del visitante
            |--------------------------------------------------------------------------
            |
            | tipo_visitante_id apunta al catálogo C_SOLTVIS:
            | VACAD, VEASOC, VEST, VOTRO.
            |
            */
            $table->foreignId('tipo_visitante_id')
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Estudiante asociado
            |--------------------------------------------------------------------------
            |
            | Solo se usa cuando tipo_visitante_id corresponde a VEASOC.
            | Se protege la trazabilidad evitando borrar estudiantes asociados
            | que ya estén ligados a solicitudes.
            |
            */
            $table->foreignId('estudiante_asociado_id')
                ->nullable()
                ->constrained('estudiantes_asociados')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Datos del visitante
            |--------------------------------------------------------------------------
            |
            | Para visitantes normales: académico/investigador, estudiante no asociado u otro.
            | Para estudiante asociado, estos datos pueden quedar nulos porque se consultan
            | desde estudiantes_asociados.
            |
            */
            $table->string('nombre')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('email')->nullable();

            $table->foreignId('pais_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            /*
             * En visitantes sí se permite institución flexible:
             * - institucion_id cuando exista en catálogo.
             * - institucion libre cuando no exista o no aplique catálogo.
             */
            $table->foreignId('institucion_id')
                ->nullable()
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->string('institucion')->nullable();
            $table->string('lugar')->nullable();

            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            /*
|--------------------------------------------------------------------------
| Auditoría institucional
|--------------------------------------------------------------------------
|
| Se usa identity_links.id para mantener trazabilidad institucional,
| consistente con el resto del módulo de solicitudes.
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
            $table->unique('solicitud_id');

            $table->index('tipo_visitante_id');
            $table->index('estudiante_asociado_id');
            $table->index('pais_id');
            $table->index('institucion_id');
            $table->index('email');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_visitantes');
    }
};