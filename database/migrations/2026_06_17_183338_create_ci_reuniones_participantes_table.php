<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ci_reuniones_participantes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reunion_id')
                ->constrained('ci_reuniones')
                ->cascadeOnDelete();

            $table->foreignId('identity_link_id')
                ->constrained('identity_links')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['reunion_id', 'identity_link_id'], 'ci_reunion_participante_unique');
            $table->index('identity_link_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ci_reuniones_participantes');
    }
};