<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ci_notificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reunion_id')
                ->nullable()
                ->constrained('ci_reuniones')
                ->nullOnDelete();

            $table->foreignId('punto_id')
                ->nullable()
                ->constrained('ci_puntos')
                ->nullOnDelete();

            $table->foreignId('solicitud_id')
                ->nullable()
                ->constrained('solicitudes')
                ->nullOnDelete();

            /*
            * Si es reenvío manual, apunta a la notificación anterior.
            */
            $table->foreignId('reenvio_de_id')
                ->nullable()
                ->constrained('ci_notificaciones')
                ->nullOnDelete();

            $table->string('tipo', 100);

            /*
            * Resolución capturada al momento del envío.
            */
            $table->string('resolucion', 50)->nullable();

            /*
            * Destinatario efectivo.
            */
            $table->string('destinatario_email');
            $table->string('destinatario_nombre')->nullable();

            /*
            * Destinatario institucional real.
            * En local permite saber a quién habría llegado.
            */
            $table->string('destinatario_real_email')->nullable();
            $table->string('destinatario_real_nombre')->nullable();

            $table->boolean('es_prueba')->default(false);

            $table->string('asunto');

            /*
            * Snapshot del contenido enviado.
            */
            $table->json('payload')->nullable();

            /*
            * Snapshot de adjuntos.
            */
            $table->json('adjuntos')->nullable();

            $table->string('estatus', 50)->default('PENDIENTE');

            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            /*
            * Reintentos técnicos del Job.
            */
            $table->unsignedInteger('intentos')->default(0);

            $table->text('error')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['punto_id', 'tipo', 'resolucion']);
            $table->index(['solicitud_id', 'tipo']);
            $table->index(['reunion_id', 'tipo']);
            $table->index(['estatus', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ci_notificaciones');
    }
};
