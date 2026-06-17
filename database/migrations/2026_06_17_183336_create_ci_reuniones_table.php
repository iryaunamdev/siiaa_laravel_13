<?php

use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ci_reuniones', function (Blueprint $table) {
            $table->id();

            $table->string('titulo');
            $table->date('fecha');

            $table->string('tipo_reunion')
                ->default(ConsejoInternoCatalogos::TIPO_REUNION_DEFAULT);

            $table->string('modalidad')
                ->default(ConsejoInternoCatalogos::MODALIDAD_DEFAULT);

            $table->string('estatus')
                ->default(ConsejoInternoCatalogos::REUNION_EN_PROCESO);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamp('concluida_at')->nullable();

            $table->foreignId('concluida_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['fecha', 'estatus']);
            $table->index('tipo_reunion');
            $table->index('modalidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ci_reuniones');
    }
};