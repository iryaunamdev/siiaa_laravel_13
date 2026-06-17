<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ci_puntos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reunion_id')
                ->constrained('ci_reuniones')
                ->cascadeOnDelete();

            $table->foreignId('tipo_punto_id')
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            /*
             * Si el punto proviene de una solicitud, solicitud_id se llena.
             * Si es un punto libre del orden del día, solicitud_id permanece null
             * y se usan titulo/descripcion.
             */
            $table->foreignId('solicitud_id')
                ->nullable()
                ->constrained('solicitudes')
                ->nullOnDelete();

            $table->string('titulo')->nullable();
            $table->text('descripcion')->nullable();

            /*
             * El orden se maneja dentro de cada tipo de punto:
             * solicitudes por un lado y otros puntos por otro.
             */
            $table->unsignedInteger('orden')->default(0);

            /*
             * Resolución final del Consejo Interno.
             * No equivale a la evaluación individual de consejeros.
             */
            $table->string('resolucion')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['reunion_id', 'tipo_punto_id', 'orden']);
            $table->index('solicitud_id');
            $table->index('resolucion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ci_puntos');
    }
};