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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('apellidop')->nullable();
            $table->string('apellidom')->nullable();

            $table->string('email')->nullable()->unique();

            $table->string('curp', 18)->nullable()->unique();
            $table->string('rfc', 13)->nullable()->unique();

            $table->date('fecha_nacimiento')->nullable();

            // Catálogos: sin FK real, solo indexados.
            $table->unsignedBigInteger('sexo_id')->nullable()->index();
            $table->unsignedBigInteger('nacionalidad_id')->nullable()->index();

            $table->boolean('activo')->default(true);

            $table->text('observaciones')->nullable();

            // Auditoría básica: sin FK real, solo indexada.
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();

            $table->index('nombre');
            $table->index('apellidop');
            $table->index('apellidom');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};