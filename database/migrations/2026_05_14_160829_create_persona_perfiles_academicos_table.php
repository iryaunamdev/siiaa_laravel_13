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
        Schema::create('persona_perfiles_academicos', function (Blueprint $table) {
            $table->id();

            // Relación interna crítica: sí lleva FK real.
            $table->foreignId('persona_id')
                ->unique()
                ->constrained('personas')
                ->restrictOnDelete();

            $table->string('orcid')->nullable()->index();

            // Catálogos: sin FK real, solo indexados.
            $table->unsignedBigInteger('sni_id')->nullable()->index();
            $table->string('sni_vigencia')->nullable();

            $table->unsignedBigInteger('pride_id')->nullable()->index();
            $table->string('pride_vigencia')->nullable();

            // Datos útiles para futuros módulos de producción académica / ADS.
            $table->text('ads_author_query')->nullable();
            $table->string('ads_profile_url')->nullable();
            $table->string('ads_library_url')->nullable();

            $table->string('scopus_id')->nullable()->index();

            $table->string('research_area')->nullable();
            $table->text('academic_keywords')->nullable();

            $table->text('observaciones')->nullable();

            // Auditoría básica: sin FK real, solo indexada.
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona_perfiles_academicos');
    }
};