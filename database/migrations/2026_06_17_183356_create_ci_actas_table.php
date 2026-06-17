<?php

use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ci_actas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reunion_id')
                ->nullable()
                ->constrained('ci_reuniones')
                ->nullOnDelete();

            /*
             * Campo manual. No hay numeración automática ni validación
             * de consecutivos en la primera versión.
             */
            $table->string('numero_acta')->nullable();

            $table->string('titulo');
            $table->date('fecha');
            $table->unsignedSmallInteger('year');

            $table->string('estatus')
                ->default(ConsejoInternoCatalogos::ACTA_BORRADOR);

            $table->json('contenido_json')->nullable();
            $table->longText('contenido_html')->nullable();

            /*
             * Texto normalizado para búsqueda. Se genera desde servicio
             * a partir de numero_acta, titulo y contenido_html.
             */
            $table->longText('search_text')->nullable();

            $table->timestamp('publicada_at')->nullable();

            $table->foreignId('publicada_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['year', 'fecha']);
            $table->index('estatus');
            $table->index('reunion_id');
            $table->index('numero_acta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ci_actas');
    }
};