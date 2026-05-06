<?php

return [

    'enabled' => env('LDAP_ENABLED', false),

    'logging' => env('LDAP_LOGGING', false),

    'connection' => env('LDAP_CONNECTION', 'default'),

    'connections' => [

        'default' => [

            'host' => env('LDAP_HOST'),
            'username' => env('LDAP_USERNAME'),
            'password' => env('LDAP_PASSWORD'),

            'port' => env('LDAP_PORT', 389),
            'base_dn' => env('LDAP_BASE_DN'),

            'timeout' => env('LDAP_TIMEOUT', 5),

            'use_ssl' => env('LDAP_SSL', false),
            'use_tls' => env('LDAP_TLS', false),
            'use_sasl' => env('LDAP_SASL', false),

        ],

    ],

    'attributes' => [
        'username' => env('LDAP_USERNAME_ATTRIBUTE', 'uid'),
        'email' => env('LDAP_EMAIL_ATTRIBUTE', 'mail'),
        'name' => env('LDAP_NAME_ATTRIBUTE', 'cn'),
    ],

];