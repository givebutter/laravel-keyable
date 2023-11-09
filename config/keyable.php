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
    | Empty Models
    |--------------------------------------------------------------------------
    |
    | Set this to true to allow API keys without an associated model.
    |
    */

    'allow_empty_models' => false,

    /*
    |--------------------------------------------------------------------------
    | Non hashed API keys mode
    |--------------------------------------------------------------------------
    |
    | Set this to true to instruct the package to accept non hashed API keys.
    | 
    | This is useful, for example, if your api_keys table has a very large
    | number of records, in such case hashing all existing API keys can take
    | a while.
    |
    | Once the new database changes are in place and all existing keys were
    | hashed, you can simply update the environment variable and have the
    | package correctly handle hashed API keys.
    |
    */

    'non_hashed_api_keys_mode' => env('NON_HASHED_API_KEYS_MODE', false),

];
