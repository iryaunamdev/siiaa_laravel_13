<?php

namespace App\Support\Solicitudes;

/**
 * Claves institucionales usadas por el módulo de Solicitudes.
 *
 * Esta clase no reemplaza los catálogos en base de datos.
 * Solo centraliza las claves oficiales para evitar strings sueltos
 * en servicios, policies, modelos y componentes Livewire.
 */
final class SolicitudCatalogos
{
    /*
    |--------------------------------------------------------------------------
    | Catálogos
    |--------------------------------------------------------------------------
    */

    public const CATALOGO_ESTATUS = 'SOL_EST';
    public const CATALOGO_TIPOS = 'SOLTIPOS';
    public const CATALOGO_MOTIVOS = 'SOLMOT';
    public const CATALOGO_TIPOS_VISITANTE = 'C_SOLTVIS';
    public const CATALOGO_REQUERIMIENTOS_VISITANTE = 'VIS_REQ';
    public const CATALOGO_ORIGENES_RECURSO = 'C_OREC';
    public const CATALOGO_DIVISAS = 'DIVISAS';
    public const CATALOGO_PAISES = 'PAISES';

    /*
    |--------------------------------------------------------------------------
    | Estatus SOL_EST
    |--------------------------------------------------------------------------
    */

    public const ESTATUS_BORRADOR = 'BORRADOR';
    public const ESTATUS_ENVIADA = 'SENV';
    public const ESTATUS_APROBADA_CI = 'APRCI';
    public const ESTATUS_RECHAZADA_CI = 'RECI';
    public const ESTATUS_TRAMITE_PAGO = 'TRPAG';
    public const ESTATUS_PAGADA = 'PAG';
    public const ESTATUS_CERRADA = 'CLO';
    public const ESTATUS_CANCELADA = 'CANCELADA';

    /**
     * Estado histórico conservado del SIIAA_10.
     * No forma parte del flujo nuevo normal.
     */
    public const ESTATUS_EN_REVISION_CIC = 'REVCIC';

    /*
    |--------------------------------------------------------------------------
    | Tipos SOLTIPOS
    |--------------------------------------------------------------------------
    */

    public const TIPO_AUSENCIA_CON_RECURSOS = 'AUS_REC';
    public const TIPO_AUSENCIA_SIN_RECURSOS = 'AUSENCIA';
    public const TIPO_RECURSOS_IRYA_ESTUDIANTE = 'ESOLREC';
    public const TIPO_SOLO_RECURSOS = 'SOLOREC';
    public const TIPO_VISITANTE = 'VISITA';

    /*
    |--------------------------------------------------------------------------
    | Motivos SOLMOT
    |--------------------------------------------------------------------------
    */

    public const MOTIVO_EVENTO_ACADEMICO = 'EVACAD';
    public const MOTIVO_ESTANCIA_TRABAJO = 'ESTT';
    public const MOTIVO_ACTIVIDAD_DIVULGACION = 'ACTDIV';
    public const MOTIVO_TRABAJO_CAMPO = 'TCAMP';
    public const MOTIVO_OTRO = 'OTRO';

    /*
    |--------------------------------------------------------------------------
    | Tipos de visitante C_SOLTVIS
    |--------------------------------------------------------------------------
    */

    public const VISITANTE_ACADEMICO = 'VACAD';
    public const VISITANTE_ESTUDIANTE_ASOCIADO = 'VEASOC';
    public const VISITANTE_ESTUDIANTE_NO_ASOCIADO = 'VEST';
    public const VISITANTE_OTRO = 'VOTRO';

    /*
    |--------------------------------------------------------------------------
    | Requerimientos VIS_REQ
    |--------------------------------------------------------------------------
    */

    public const REQ_OFICINA = 'REQ_OF';
    public const REQ_CUENTA_COMPUTO = 'REQ_CCOP';
    public const REQ_EQUIPO_COMPUTO = 'REQ_ECOP';
    public const REQ_ACCESO_ACERVO = 'REQ_ACER';
    public const REQ_COLOQUIO = 'D_COLOQ';

    /*
    |--------------------------------------------------------------------------
    | Orígenes de recurso C_OREC
    |--------------------------------------------------------------------------
    */

    public const ORIGEN_PROYECTO_INTERNO = 'R_PI';
    public const ORIGEN_PAPIIT = 'R_PAPIIT';
    public const ORIGEN_PAPIME = 'R_PAPIME';
    public const ORIGEN_CONVENIO = 'CONV';
    public const ORIGEN_IRYA = 'R_IRYA';
    public const ORIGEN_SECIHT = 'SECIHT';

    /*
    |--------------------------------------------------------------------------
    | Divisas DIVISAS
    |--------------------------------------------------------------------------
    */

    public const DIVISA_MXN = 'MXN';
    public const DIVISA_USD = 'USD';
    public const DIVISA_EUR = 'EUR';

    /*
    |--------------------------------------------------------------------------
    | Países PAISES
    |--------------------------------------------------------------------------
    */

    public const PAIS_MEXICO = 'MEX';

    /*
    |--------------------------------------------------------------------------
    | Versiones de política
    |--------------------------------------------------------------------------
    */

    public const POLITICA_VERSION_ACTUAL = 'SIIAA_13';
    public const POLITICA_VERSION_SIIAA_10 = 'SIIAA_10';

    /*
    |--------------------------------------------------------------------------
    | Agrupadores útiles
    |--------------------------------------------------------------------------
    */

    public static function estatusEditablesPorPropietario(): array
    {
        return [
            self::ESTATUS_BORRADOR,
            self::ESTATUS_ENVIADA,
        ];
    }

    public static function estatusBloqueadosParaPropietario(): array
    {
        return [
            self::ESTATUS_APROBADA_CI,
            self::ESTATUS_RECHAZADA_CI,
            self::ESTATUS_TRAMITE_PAGO,
            self::ESTATUS_PAGADA,
            self::ESTATUS_CERRADA,
            self::ESTATUS_CANCELADA,
        ];
    }

    public static function tiposConRecursosObligatorios(): array
    {
        return [
            self::TIPO_AUSENCIA_CON_RECURSOS,
            self::TIPO_RECURSOS_IRYA_ESTUDIANTE,
            self::TIPO_SOLO_RECURSOS,
        ];
    }

    public static function tiposSinRecursos(): array
    {
        return [
            self::TIPO_AUSENCIA_SIN_RECURSOS,
        ];
    }

    public static function tiposConRecursosSeleccionables(): array
    {
        return [
            self::TIPO_VISITANTE,
        ];
    }

    public static function tiposSolicitud(): array
    {
        return [
            self::TIPO_AUSENCIA_CON_RECURSOS,
            self::TIPO_AUSENCIA_SIN_RECURSOS,
            self::TIPO_RECURSOS_IRYA_ESTUDIANTE,
            self::TIPO_SOLO_RECURSOS,
            self::TIPO_VISITANTE,
        ];
    }

    public static function motivosGenerales(): array
    {
        return [
            self::MOTIVO_EVENTO_ACADEMICO,
            self::MOTIVO_ESTANCIA_TRABAJO,
            self::MOTIVO_ACTIVIDAD_DIVULGACION,
            self::MOTIVO_TRABAJO_CAMPO,
            self::MOTIVO_OTRO,
        ];
    }

    public static function tiposVisitante(): array
    {
        return [
            self::VISITANTE_ACADEMICO,
            self::VISITANTE_ESTUDIANTE_ASOCIADO,
            self::VISITANTE_ESTUDIANTE_NO_ASOCIADO,
            self::VISITANTE_OTRO,
        ];
    }

    public static function requerimientosVisitante(): array
    {
        return [
            self::REQ_OFICINA,
            self::REQ_CUENTA_COMPUTO,
            self::REQ_EQUIPO_COMPUTO,
            self::REQ_ACCESO_ACERVO,
            self::REQ_COLOQUIO,
        ];
    }

    public static function origenesRecurso(): array
    {
        return [
            self::ORIGEN_PROYECTO_INTERNO,
            self::ORIGEN_PAPIIT,
            self::ORIGEN_PAPIME,
            self::ORIGEN_CONVENIO,
            self::ORIGEN_IRYA,
            self::ORIGEN_SECIHT,
        ];
    }

    public static function divisas(): array
    {
        return [
            self::DIVISA_MXN,
            self::DIVISA_USD,
            self::DIVISA_EUR,
        ];
    }

    public static function requiereMotivo(?string $tipoClave): bool
    {
        return $tipoClave !== self::TIPO_VISITANTE;
    }

    public static function requiereRecursosPorTipo(?string $tipoClave): bool
    {
        return in_array($tipoClave, self::tiposConRecursosObligatorios(), true);
    }

    public static function noRequiereRecursosPorTipo(?string $tipoClave): bool
    {
        return in_array($tipoClave, self::tiposSinRecursos(), true);
    }

    public static function recursosSeleccionablesPorTipo(?string $tipoClave): bool
    {
        return in_array($tipoClave, self::tiposConRecursosSeleccionables(), true);
    }
}