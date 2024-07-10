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

    'key-header' => null,

    'key-parameter' => null,

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
    | Compatibility mode
    |--------------------------------------------------------------------------
    |
    | Set this to true to instruct this package to accept both hashed and non
    | hashed API keys.
    |
    | This is useful to keep your app running smoothly while you are going
    | throught the upgrade steps for version 2.1.1 to 3.0.0, especially if you
    | have a very large api_keys table, which can take a while to hash all
    | existing API keys.
    |
    | Once the new database changes are in place and all existing keys are
    | hashed, you should set this flag to false to instruct this package to
    | only look for hashed API keys.
    |
    */

    'compatibility_mode' => env('KEYABLE_COMPATIBILITY_MODE', false),

];
