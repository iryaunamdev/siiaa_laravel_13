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
        Schema::create('persona_ingresos', function (Blueprint $table) {
            $table->id();

            // Relación interna crítica: sí lleva FK real.
            $table->foreignId('persona_id')
                ->constrained('personas')
                ->restrictOnDelete();

            // Catálogos: sin FK real, solo indexados.
            $table->unsignedBigInteger('tipo_personal_id')->nullable()->index();

            $table->string('numero_trabajador')->nullable()->index();
            $table->string('cuv')->nullable()->index();

            // Catálogos: sin FK real, solo indexados.
            $table->unsignedBigInteger('contrato_id')->nullable()->index();
            $table->unsignedBigInteger('nombramiento_id')->nullable()->index();
            $table->unsignedBigInteger('categoria_id')->nullable()->index();
            $table->unsignedBigInteger('escolaridad_id')->nullable()->index();

            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_nombramiento')->nullable();
            $table->date('fecha_definitividad')->nullable();
            $table->date('fecha_baja')->nullable();

            $table->boolean('activo')->default(true);
            $table->boolean('principal')->default(false);

            $table->text('observaciones')->nullable();

            // Auditoría básica: sin FK real, solo indexada.
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();

            $table->index('activo');
            $table->index('principal');
            $table->index(['persona_id', 'activo']);
            $table->index(['persona_id', 'principal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona_ingresos');
    }
};