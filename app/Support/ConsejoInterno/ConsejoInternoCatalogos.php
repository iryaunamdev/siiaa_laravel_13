<?php

namespace App\Support\ConsejoInterno;

/**
 * Claves institucionales usadas por el módulo Consejo Interno.
 *
 * Esta clase no reemplaza los catálogos en base de datos. Solo centraliza
 * claves y valores operativos para evitar strings sueltos en servicios,
 * policies, modelos y componentes Livewire.
 */
final class ConsejoInternoCatalogos
{
    /*
    |--------------------------------------------------------------------------
    | Catálogos
    |--------------------------------------------------------------------------
    */

    public const CATALOGO_TIPOS_PUNTO = 'C_RCI_TP';
    public const CATALOGO_EVALUACIONES = 'C_RCI_EVA';

    /*
    |--------------------------------------------------------------------------
    | Tipos de punto C_RCI_TP
    |--------------------------------------------------------------------------
    */

    public const TIPO_PUNTO_SOLICITUD = 'TP_SOL';
    public const TIPO_PUNTO_OTRO = 'TP_OTRO';

    /*
    |--------------------------------------------------------------------------
    | Evaluaciones C_RCI_EVA
    |--------------------------------------------------------------------------
    */

    public const EVALUACION_ACEPTAR = 'EV_A';
    public const EVALUACION_DISCUTIR = 'EV_DIS';
    public const EVALUACION_RECHAZAR = 'EV_RE';

    /*
    |--------------------------------------------------------------------------
    | Estados de reunión
    |--------------------------------------------------------------------------
    */

    public const REUNION_EN_PROCESO = 'EN_PROCESO';
    public const REUNION_CONCLUIDA = 'CONCLUIDA';

    /*
    |--------------------------------------------------------------------------
    | Estados de acta
    |--------------------------------------------------------------------------
    */

    public const ACTA_BORRADOR = 'BORRADOR';
    public const ACTA_PUBLICADA = 'PUBLICADA';

    /*
    |--------------------------------------------------------------------------
    | Resoluciones finales de puntos
    |--------------------------------------------------------------------------
    |
    | La resolución final es texto en ci_puntos. No equivale a la evaluación
    | individual de cada consejero.
    |
    */

    public const RESOLUCION_ACEPTAR = 'ACEPTAR';
    public const RESOLUCION_DISCUTIR = 'DISCUTIR';
    public const RESOLUCION_RECHAZAR = 'RECHAZAR';

    /*
    |--------------------------------------------------------------------------
    | Valores por defecto
    |--------------------------------------------------------------------------
    */

    public const TIPO_REUNION_DEFAULT = 'ORDINARIA';
    public const MODALIDAD_DEFAULT = 'PRESENCIAL';

    public static function tiposPunto(): array
    {
        return [
            self::TIPO_PUNTO_SOLICITUD,
            self::TIPO_PUNTO_OTRO,
        ];
    }

    public static function evaluaciones(): array
    {
        return [
            self::EVALUACION_ACEPTAR,
            self::EVALUACION_DISCUTIR,
            self::EVALUACION_RECHAZAR,
        ];
    }

    public static function estadosReunion(): array
    {
        return [
            self::REUNION_EN_PROCESO,
            self::REUNION_CONCLUIDA,
        ];
    }

    public static function estadosActa(): array
    {
        return [
            self::ACTA_BORRADOR,
            self::ACTA_PUBLICADA,
        ];
    }

    public static function resolucionesFinales(): array
    {
        return [
            self::RESOLUCION_ACEPTAR,
            self::RESOLUCION_DISCUTIR,
            self::RESOLUCION_RECHAZAR,
        ];
    }
}