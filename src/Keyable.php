<?php

namespace Givebutter\LaravelKeyable;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Illuminate\Support\Facades\Hash;

trait Keyable
{
    public function apiKeys()
    {
        return $this->morphMany(ApiKey::class, 'keyable');
    }

    public function createApiKey(array $attributes = []): NewApiKey
    {
        $planTextApiKey = ApiKey::generate();

        $apiKey = $this->apiKeys()->create([
            'key' => hash('sha256', $planTextApiKey),
            'name' => $attributes['name'] ?? null,
        ]);

        return new NewApiKey($apiKey, "{$apiKey->getKey()}|{$planTextApiKey}");
    }
}
