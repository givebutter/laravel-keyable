<?php
    
namespace Givebutter\LaravelKeyable\Facades;

use Illuminate\Support\Facades\Facade;
use Givebutter\LaravelKeyable\Auth\Keyable as KeyableAuth;

class Keyable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return KeyableAuth::class;
    }
}
