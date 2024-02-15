<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'cytomine' => [
        'admin_username' => env('CYTOMINE_ADMIN_USERNAME'),
        'admin_password' => env('CYTOMINE_ADMIN_PASSWORD'),
        'auth_url' => env('CYTOMINE_AUTH_URL'),
        'api_url' => env('CYTOMINE_API_URL'),
        'core_url' => env('CYTOMINE_CORE_URL'),
        'ontology' => env('CYTOMINE_DEFAULT_ONTOLOGY', 235359),
    ],
    'scanner' => [
        'api_url' => env('SCANNER_API_URL'),
        'speed' => env('SCANNER_SPEED'),
    ],
    'yun' => [
        'api_key' => env('YUN_API_KEY'),
        'api_url' => env('YUN_API_URL'),
    ]

];
