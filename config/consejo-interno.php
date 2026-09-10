<?php

return [

    'mail' => [

        /*
        |--------------------------------------------------------------------------
        | Destinatario de pruebas
        |--------------------------------------------------------------------------
        |
        | Production:
        |   Se utiliza el correo real del solicitante.
        |
        | Cualquier otro ambiente:
        |   Se redirige obligatoriamente al correo de pruebas.
        |
        */

        'test_recipient' => env('CI_MAIL_TEST_RECIPIENT'),
        'test_name' => env(
            'CI_MAIL_TEST_NAME',
            'Pruebas Consejo Interno'
        ),

        /*
        |--------------------------------------------------------------------------
        | Cola
        |--------------------------------------------------------------------------
        */

        'queue' => env(
            'CI_MAIL_QUEUE',
            'consejo-interno'
        ),

        /*
        |--------------------------------------------------------------------------
        | Documentos adjuntos
        |--------------------------------------------------------------------------
        |
        | No conviene enviar automáticamente documentación interna de reunión
        | sin una decisión explícita.
        |
        */

        'attach_reunion_documents' => env(
            'CI_MAIL_ATTACH_REUNION_DOCUMENTS',
            false
        ),
    ],

];
