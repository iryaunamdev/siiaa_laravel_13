<?php

namespace App\Services\ConsejoInterno;

use App\Models\ConsejoInterno\CiActa;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CiActaService implements CiActaServiceInterface
{
    public function crear(
        array $data,
        ?int $identityId
    ): CiActa {
        return DB::transaction(function () use ($data, $identityId) {
            $payload = $this->normalizarDatos($data);

            $payload['estatus'] = ConsejoInternoCatalogos::ACTA_BORRADOR;
            $payload['created_by'] = $identityId;
            $payload['updated_by'] = $identityId;

            return CiActa::query()->create($payload);
        });
    }

    public function actualizar(
        CiActa $acta,
        array $data,
        ?int $identityId
    ): CiActa {
        return DB::transaction(function () use ($acta, $data, $identityId) {
            $payload = $this->normalizarDatos($data);

            $acta->fill($payload);

            if ($acta->isDirty()) {
                $acta->updated_by = $identityId;
                $acta->save();
            }

            return $acta->refresh();
        });
    }

    public function eliminar(CiActa $acta): void
    {
        DB::transaction(function () use ($acta) {
            $acta->delete();
        });
    }

    public function publicar(
        CiActa $acta,
        ?int $identityId
    ): void {
        DB::transaction(function () use ($acta, $identityId) {
            if ($acta->estaPublicada()) {
                return;
            }

            $acta->forceFill([
                'estatus' => ConsejoInternoCatalogos::ACTA_PUBLICADA,
                'publicada_at' => now(),
                'publicada_by' => $identityId,
                'updated_by' => $identityId,
            ])->save();
        });
    }

    private function normalizarDatos(array $data): array
    {
        $payload = Arr::only($data, [
            'reunion_id',
            'numero_acta',
            'titulo',
            'fecha',
            'contenido_json',
            'contenido_html',
            'search_text',
        ]);

        $payload['reunion_id'] = filled($payload['reunion_id'] ?? null)
            ? (int) $payload['reunion_id']
            : null;

        $payload['numero_acta'] = trim(
            (string) ($payload['numero_acta'] ?? '')
        );

        $payload['titulo'] = trim(
            (string) ($payload['titulo'] ?? '')
        );

        $payload['year'] = filled($payload['fecha'] ?? null)
            ? (int) date('Y', strtotime($payload['fecha']))
            : null;

        return $payload;
    }
}
