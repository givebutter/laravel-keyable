<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Mode
    |--------------------------------------------------------------------------
    |
    | Supported modes: header, bearer, parameter
    |
    | When using header or parameter, set a key value.
    |
    */

    'mode' => 'bearer',

    'key' => null,

    /*
    |--------------------------------------------------------------------------
    | Identifier mode
    |--------------------------------------------------------------------------
    |
    | Supported modes: bigint (increments), uuid (uuid)
    |
    | This value defines which type of identifier do you want to use
    |
    */

    'identifier' => 'bigint',

    /*
    |--------------------------------------------------------------------------
    | Uuid configuration
    |--------------------------------------------------------------------------
    |
    | Supported modes: 1, 2, 3, 4
    |
    | This value defines which type of uuid do you want to use if you use
    | uuid identifiers.
    |
    */

    'uuid' => [
        'version' => 4
    ],

    /*
    |--------------------------------------------------------------------------
    | Empty Models
    |--------------------------------------------------------------------------
    |
    | Set this to true to allow API keys without an associated model.
    |
    */

    'allow_empty_models' => false,
];
