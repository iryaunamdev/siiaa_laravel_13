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
        Schema::create('identity_links', function (Blueprint $table) {
            // ID determinístico, no autoincremental.
            $table->unsignedBigInteger('id')->primary();

            $table->string('identity_type', 50); // siiaa, siiap_student
            $table->unsignedBigInteger('identity_id');

            // Correo de referencia para resolución de identidad.
            // No es único porque la unicidad real la controla identity_type + identity_id.
            $table->string('email')->nullable()->index();

            $table->boolean('is_primary')->default(false);
            $table->boolean('active')->default(true);

            $table->string('matched_by')->nullable(); // email, manual, import, ldap, siiap
            $table->timestamp('matched_at')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->text('observaciones')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['identity_type', 'identity_id']);

            $table->index(['active', 'identity_type']);
            $table->index(['identity_type', 'email']);
            $table->index(['identity_type', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identity_links');
    }
};