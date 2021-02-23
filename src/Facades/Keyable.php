<?php

namespace Givebutter\LaravelKeyable\Facades;

use Illuminate\Support\Facades\Facade;
use Givebutter\LaravelKeyable\Auth\Keyable as KeyableAuth;

/**
 * Class Keyable
 * @method object getKeyablePolicies
 */
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
