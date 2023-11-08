<?php

namespace Givebutter\LaravelKeyable;

use Givebutter\LaravelKeyable\Models\ApiKey;

class NewApiKey
{
    public function __construct(
        public ApiKey $apiKey,
        public string $plainTextApiKey,
    ) {
        //
    }
}
