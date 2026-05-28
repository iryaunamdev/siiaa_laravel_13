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
        Schema::create('perfiles_publicos', function (Blueprint $table) {
            $table->id();

            // Referencia lógica a identity_links.id.
            // Sin FK real por política de flexibilidad operativa.
            $table->unsignedBigInteger('identity_link_id')->unique();

            $table->string('photo_path')->nullable();

            $table->string('titulo_es')->nullable();
            $table->string('titulo_en')->nullable();

            $table->string('nombre_publico')->nullable();
            $table->string('apellido_publico')->nullable();

            $table->string('area_es')->nullable();
            $table->string('area_en')->nullable();

            $table->string('oficina')->nullable();

            $table->string('extension_red_unam')->nullable();
            $table->string('telefono_morelia')->nullable();
            $table->string('telefono_cdmx')->nullable();

            $table->string('email_publico')->nullable();
            $table->string('homepage_url')->nullable();

            $table->boolean('active')->default(true);
            $table->boolean('visible')->default(false);

            $table->unsignedInteger('sort_order')->nullable();

            $table->text('observaciones')->nullable();

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();

            $table->index('identity_link_id');
            $table->index(['active', 'visible']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfiles_publicos');
    }
};