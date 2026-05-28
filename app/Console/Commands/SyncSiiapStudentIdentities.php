<?php

namespace App\Console\Commands;

use App\Models\Siiap\Estudiante;
use App\Services\Identity\SiiapStudentIdentityService;
use Illuminate\Console\Command;

class SyncSiiapStudentIdentities extends Command
{
    protected $signature = 'siiaa:sync-siiap-student-identities
                            {--limit= : Limitar la cantidad de estudiantes a procesar}
                            {--dry-run : Simular sin guardar cambios}';

    protected $description = 'Actualiza periódicamente identidades de estudiantes activos SIIAP hacia identity_links.';

    public function handle(SiiapStudentIdentityService $identityService): int
    {
        $limit = $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');

        $query = Estudiante::query()
            ->activosIrya()
            ->ordenadoPorNombre();

        if ($limit !== null) {
            $query->limit((int) $limit);
        }

        $estudiantes = $query->get();

        if ($estudiantes->isEmpty()) {
            $this->warn('No se encontraron estudiantes activos IRyA en SIIAP.');

            return self::SUCCESS;
        }

        $this->info('Estudiantes activos encontrados: ' . $estudiantes->count());

        if ($dryRun) {
            $this->warn('Modo simulación activo. No se guardarán cambios.');
        }

        $processed = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($estudiantes->count());
        $bar->start();

        foreach ($estudiantes as $estudiante) {
            try {
                if (! $dryRun) {
                    $identityService->sync($estudiante);
                }

                $processed++;
            } catch (\Throwable $e) {
                $errors++;

                $this->newLine();
                $this->error(
                    'Error con estudiante ID ' . $estudiante->id . ': ' . $e->getMessage()
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
            'Nota: este comando es de mantenimiento periódico. ' .
                'La creación bajo demanda durante login sigue a cargo de IdentityResolverService.'
        );

        return self::SUCCESS;
    }
}
