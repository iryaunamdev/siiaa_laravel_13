<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ci_puntos_evaluaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('punto_id')
                ->constrained('ci_puntos')
                ->cascadeOnDelete();

            $table->foreignId('identity_link_id')
                ->constrained('identity_links')
                ->cascadeOnDelete();

            $table->foreignId('evaluacion_id')
                ->constrained('catalogos_items')
                ->restrictOnDelete();

            $table->text('comentarios')->nullable();

            $table->timestamps();

            $table->unique(['punto_id', 'identity_link_id'], 'ci_punto_evaluacion_unique');
            $table->index('evaluacion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ci_puntos_evaluaciones');
    }
};