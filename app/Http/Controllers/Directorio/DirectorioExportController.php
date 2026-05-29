<?php

namespace App\Http\Controllers\Directorio;

use App\Http\Controllers\Controller;
use App\Services\Directorio\DirectorioQueryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DirectorioExportController extends Controller
{
    public function __invoke(
        Request $request,
        string $format,
        DirectorioQueryService $directorio
    ) {
        abort_unless(
            in_array($format, ['csv', 'json', 'xlsx'], true),
            404
        );

        $filters = [
            'search' => $request->query('q'),
            'tipo' => $request->query('tipo', 'todos'),
            'estado_perfil' => $request->query('estado', 'todos'),
        ];

        $rows = $directorio->get($filters);

        return match ($format) {
            'csv' => $this->csv($rows),
            'json' => $this->json($rows),
            'xlsx' => $this->xlsx($rows),
        };
    }

    protected function json($rows)
    {
        return response()->json([
            'generated_at' => now()->toISOString(),
            'total' => $rows->count(),
            'data' => $rows->values(),
        ]);
    }

    protected function csv($rows): StreamedResponse
    {
        $filename = 'directorio-irya-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // BOM para Excel en Windows.
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, $this->headings());

            foreach ($rows as $row) {
                fputcsv($handle, $this->mapRow($row));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function xlsx($rows)
    {
        /*
         * Primera versión ligera:
         * exportamos como CSV si todavía no instalamos Laravel Excel.
         *
         * Si ya tienes maatwebsite/excel instalado, en el siguiente paso
         * lo cambiamos por un XLSX real.
         */
        return $this->csv($rows);
    }

    protected function headings(): array
    {
        return [
            'identity_id',
            'identity_type',
            'directorio_tipo',
            'directorio_tipo_label',
            'estado_institucional',
            'titulo_es',
            'titulo_en',
            'nombre_publico',
            'apellido_publico',
            'nombre_completo',
            'area_es',
            'area_en',
            'oficina',
            'extension_red_unam',
            'telefono_morelia',
            'telefono_cdmx',
            'email_publico',
            'homepage_url',
            'orcid',
            'scopus_id',
            'ads_author_query',
            'ads_profile_url',
            'ads_library_url',
            'research_area',
            'academic_keywords',
            'visible',
            'active',
        ];
    }

    protected function mapRow(array $row): array
    {
        return [
            $row['identity_id'] ?? null,
            $row['identity_type'] ?? null,
            $row['directorio_tipo'] ?? null,
            $row['directorio_tipo_label'] ?? null,
            $row['estado_institucional'] ?? null,
            $row['titulo_es'] ?? null,
            $row['titulo_en'] ?? null,
            $row['nombre_publico'] ?? null,
            $row['apellido_publico'] ?? null,
            $row['nombre_completo'] ?? null,
            $row['area_es'] ?? null,
            $row['area_en'] ?? null,
            $row['oficina'] ?? null,
            $row['extension_red_unam'] ?? null,
            $row['telefono_morelia'] ?? null,
            $row['telefono_cdmx'] ?? null,
            $row['email_publico'] ?? null,
            $row['homepage_url'] ?? null,
            $row['orcid'] ?? null,
            $row['scopus_id'] ?? null,
            $row['ads_author_query'] ?? null,
            $row['ads_profile_url'] ?? null,
            $row['ads_library_url'] ?? null,
            $row['research_area'] ?? null,
            $row['academic_keywords'] ?? null,
            $row['visible'] ?? false,
            $row['active'] ?? false,
        ];
    }
}
