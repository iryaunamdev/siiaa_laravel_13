<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique();
            $table->longText('value')->nullable();

            $table->string('group')->nullable()->index();
            $table->string('type')->default('string');

            $table->string('label')->nullable();
            $table->text('description')->nullable();

            $table->boolean('is_public')->default(false);
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};