<?php

namespace App\Console\Commands;

use App\Models\IdentityLink;
use App\Services\Perfiles\PerfilPublicoService;
use Illuminate\Console\Command;

class SyncPublicProfiles extends Command
{
    protected $signature = 'siiaa:sync-public-profiles
                            {--type= : Tipo de identidad a procesar, por ejemplo siiap_student o siiaa}
                            {--visible : Crear perfiles como visibles}
                            {--dry-run : Simular sin guardar cambios}';

    protected $description = 'Crea o actualiza perfiles públicos desde identidades activas registradas en identity_links.';

    public function handle(PerfilPublicoService $perfilService): int
    {
        $identityType = $this->option('type');
        $visible = (bool) $this->option('visible');
        $dryRun = (bool) $this->option('dry-run');

        $query = IdentityLink::query()
            ->where('active', true)
            ->orderBy('identity_type')
            ->orderBy('id');

        if ($identityType) {
            $query->where('identity_type', $identityType);
        }

        $identities = $query->get();

        if ($identities->isEmpty()) {
            $this->warn('No se encontraron identidades activas para procesar.');

            return self::SUCCESS;
        }

        $this->info('Identidades activas encontradas: ' . $identities->count());

        if ($identityType) {
            $this->line('Tipo de identidad: ' . $identityType);
        }

        if ($dryRun) {
            $this->warn('Modo simulación activo. No se guardarán cambios.');
        }

        if ($visible) {
            $this->warn('Los perfiles nuevos se crearán como visibles.');
        } else {
            $this->comment('Los perfiles nuevos se crearán como no visibles.');
        }

        $processed = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($identities->count());
        $bar->start();

        foreach ($identities as $identity) {
            try {
                if (! $dryRun) {
                    $perfilService->firstOrCreateAndFillFromIdentity(
                        identity: $identity,
                        visible: $visible
                    );
                }

                $processed++;
            } catch (\Throwable $e) {
                $errors++;

                $this->newLine();
                $this->error(
                    'Error con identity_link_id ' . $identity->id . ': ' . $e->getMessage()
                );
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Identidades revisadas: ' . $processed);
        $this->info('Errores: ' . $errors);

        $this->line('');
        $this->comment(
            'Nota: este comando crea o completa perfiles públicos, pero no sustituye la administración manual de visibilidad.'
        );

        return self::SUCCESS;
    }
}
