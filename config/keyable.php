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
	
];