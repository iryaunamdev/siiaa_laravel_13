<?php

namespace App\Http\Controllers\Directorio;

use App\Http\Controllers\Controller;
use App\Services\Directorio\DirectorioQueryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DirectorioPublicFeedController extends Controller
{
    public function __invoke(
        Request $request,
        string $format,
        DirectorioQueryService $directorio
    ) {
        abort_unless(
            in_array($format, ['json', 'csv'], true),
            404
        );

        $filters = [
            'search' => $request->query('q'),
            'tipo' => $request->query('tipo', 'todos'),
            'estado_perfil' => 'visible',
            'public_only' => true,
        ];

        $rows = $directorio->getPublic($filters);

        return match ($format) {
            'json' => $this->json($rows),
            'csv' => $this->csv($rows),
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
        $filename = 'directorio-publico-irya-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

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

    protected function headings(): array
    {
        return [
            'id',
            'tipo',
            'tipo_label',
            'titulo_es',
            'titulo_en',
            'nombre',
            'apellido',
            'nombre_completo',
            'area_es',
            'area_en',
            'oficina',
            'extension_red_unam',
            'telefono_morelia',
            'telefono_cdmx',
            'email',
            'homepage_url',
            'orcid',
            'ads_author_query',
            'ads_profile_url',
            'ads_library_url',
        ];
    }

    protected function mapRow(array $row): array
    {
        return [
            $row['id'] ?? null,
            $row['tipo'] ?? null,
            $row['tipo_label'] ?? null,
            $row['titulo_es'] ?? null,
            $row['titulo_en'] ?? null,
            $row['nombre'] ?? null,
            $row['apellido'] ?? null,
            $row['nombre_completo'] ?? null,
            $row['area_es'] ?? null,
            $row['area_en'] ?? null,
            $row['oficina'] ?? null,
            $row['extension_red_unam'] ?? null,
            $row['telefono_morelia'] ?? null,
            $row['telefono_cdmx'] ?? null,
            $row['email'] ?? null,
            $row['homepage_url'] ?? null,
            $row['orcid'] ?? null,
            $row['ads_author_query'] ?? null,
            $row['ads_profile_url'] ?? null,
            $row['ads_library_url'] ?? null,
        ];
    }
}
