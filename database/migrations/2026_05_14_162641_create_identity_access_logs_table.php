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
        Schema::create('identity_access_logs', function (Blueprint $table) {
            $table->id();

            // Usuario real autenticado.
            // Sin FK real por política de flexibilidad operativa.
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Identidad institucional usada durante la sesión.
            // Referencia lógica a identity_links.id.
            $table->unsignedBigInteger('identity_link_id')->nullable()->index();

            // Tipo de acceso: normal, impersonation, manual_test.
            $table->string('access_type', 50)->default('normal')->index();

            // En impersonation, usuario real que activa la suplantación.
            // Normalmente será el mismo user_id, pero se separa para claridad de auditoría.
            $table->unsignedBigInteger('impersonated_by')->nullable()->index();

            // Motivo obligatorio para impersonation/manual_test.
            $table->text('reason')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('ended_at')->nullable()->index();

            $table->timestamps();

            $table->index(['user_id', 'access_type']);
            $table->index(['identity_link_id', 'access_type']);
            $table->index(['access_type', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity_access_logs');
    }
};