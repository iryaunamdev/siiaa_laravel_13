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
        Schema::create('persona_posdoc_becas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('persona_id')->index();
            $table->unsignedBigInteger('beca_id')->nullable()->index();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();

            $table->unsignedBigInteger('asesor_id')->nullable()->index();

            $table->boolean('principal')->default(false);
            $table->boolean('activo')->default(true);

            $table->text('observaciones')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['persona_id', 'activo']);
            $table->index(['persona_id', 'principal']);
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona_posdoc_becas');
    }
};