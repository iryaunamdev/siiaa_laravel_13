<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogos_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('catalogo_id')
                ->constrained('catalogos')
                ->cascadeOnDelete();

            $table->integer('orden')->nullable();

            $table->string('clave', 30);
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            $table->boolean('activo')->default(true);

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->unique(['catalogo_id', 'clave']);
            $table->index(['catalogo_id', 'activo']);
            $table->index(['catalogo_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogos_items');
    }
};
