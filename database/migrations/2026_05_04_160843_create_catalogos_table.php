<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();

            $table->string('clave', 30)->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            $table->boolean('activo')->default(true);

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};
