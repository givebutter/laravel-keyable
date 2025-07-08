<?php

namespace Givebutter\LaravelKeyable\Events;

use Givebutter\LaravelKeyable\Models\ApiKey;

class KeyableAuthenticated
{
    public function __construct(public ApiKey $apiKey)
    {
    }
}
