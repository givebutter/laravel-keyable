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
    | Empty Models
    |--------------------------------------------------------------------------
    |
    | Set this to true to allow API keys without an associated model.
    |
    */

    'allow_empty_models' => false,
];
