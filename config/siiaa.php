<?php

return [
    'mail' => [
        /*
        |--------------------------------------------------------------------------
        | Uso de destinatarios reales
        |--------------------------------------------------------------------------
        |
        | En producción debe ser true.
        | En local/desarrollo normalmente debe ser false para redirigir correos
        | a destinatarios de prueba.
        |
        */
        'use_real_recipients' => env('APP_ENV') === 'production',
        /*
        |--------------------------------------------------------------------------
        | Correos de prueba
        |--------------------------------------------------------------------------
        |
        | Se usan cuando use_real_recipients=false.
        |
        */
        'test_recipients' => array_filter(array_map(
            'trim',
            explode(',', env('SIIAA_MAIL_TEST_RECIPIENTS', ''))
        )),

        /*
        |--------------------------------------------------------------------------
        | Consejo Interno
        |--------------------------------------------------------------------------
        |
        | Destinatarios reales del Consejo Interno para solicitudes enviadas.
        |
        */
        'consejo_interno' => array_filter(array_map(
            'trim',
            explode(',', env('SIIAA_MAIL_CI_RECIPIENTS', ''))
        )),
    ],
];