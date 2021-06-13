<?php

namespace Givebutter\LaravelKeyable\Facades;

use Givebutter\LaravelKeyable\Auth\Keyable as KeyableAuth;
use Illuminate\Support\Facades\Facade;

class Keyable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return KeyableAuth::class;
    }
}
