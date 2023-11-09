<?php

namespace Givebutter\Tests\Unit\Models;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Givebutter\Tests\Support\Account;
use Givebutter\Tests\TestCase;

class ApiKeyTest extends TestCase
{
    /** @test */
    public function create_new_api_key(): void
    {
        $account = Account::create();

        $apiKey = ApiKey::create([
            'keyable_id' => $account->getKey(),
            'keyable_type' => Account::class,
            'name' => 'my api key',
        ]);

        $this->assertDatabaseHas('api_keys', [
            'key' => hash('sha256', $apiKey->plainTextApiKey),
            'keyable_id' => $account->getKey(),
            'keyable_type' => Account::class,
            'name' => 'my api key',
        ]);
    }
}
