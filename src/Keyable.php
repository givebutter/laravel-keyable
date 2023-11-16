<?php

namespace Givebutter\LaravelKeyable;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Illuminate\Database\Eloquent\Model;

trait Keyable
{
    public function apiKeys()
    {
        return $this->morphMany(ApiKey::class, 'keyable');
    }

    public function createApiKey(array $attributes = []): NewApiKey
    {
        $planTextApiKey = ApiKey::generate();

        $apiKey = Model::withoutEvents(function () use ($planTextApiKey, $attributes) {
            return $this->apiKeys()->create([
                'key' => hash('sha256', $planTextApiKey),
                'name' => $attributes['name'] ?? null,
            ]);
        });

        return new NewApiKey($apiKey, "{$apiKey->getKey()}|{$planTextApiKey}");
    }
}
