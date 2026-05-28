<?php

use App\Models\IdentityLink;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persona_perfiles_academicos', function (Blueprint $table) {
            $table->unsignedBigInteger('identity_link_id')
                ->nullable()
                ->after('id')
                ->index();
        });

        /*
         * Se migra la relación actual persona_id -> identity_link_id.
         * Se usa la convención establecida para identidades SIIAA:
         * identity_links.id = 10000000 + personas.id
         */
        DB::table('persona_perfiles_academicos')
            ->whereNotNull('persona_id')
            ->orderBy('id')
            ->chunkById(100, function ($perfiles) {
                foreach ($perfiles as $perfil) {
                    $identityLinkId = IdentityLink::makeIdentityId(
                        IdentityLink::TYPE_SIIAA,
                        (int) $perfil->persona_id
                    );

                    DB::table('persona_perfiles_academicos')
                        ->where('id', $perfil->id)
                        ->update([
                            'identity_link_id' => $identityLinkId,
                        ]);
                }
            });

        Schema::table('persona_perfiles_academicos', function (Blueprint $table) {
            $table->unique('identity_link_id', 'persona_perfiles_academicos_identity_unique');
        });
    }

    public function down(): void
    {
        Schema::table('persona_perfiles_academicos', function (Blueprint $table) {
            $table->dropUnique('persona_perfiles_academicos_identity_unique');
            $table->dropColumn('identity_link_id');
        });
    }
};