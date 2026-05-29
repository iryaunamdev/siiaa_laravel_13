<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perfiles_publicos', function (Blueprint $table) {
            $table->string('directorio_tipo', 80)
                ->nullable()
                ->after('identity_link_id')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('perfiles_publicos', function (Blueprint $table) {
            $table->dropIndex(['directorio_tipo']);
            $table->dropColumn('directorio_tipo');
        });
    }
};