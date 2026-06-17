<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ci_documentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reunion_id')
                ->constrained('ci_reuniones')
                ->cascadeOnDelete();

            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('identity_links')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('reunion_id');
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ci_documentos');
    }
};