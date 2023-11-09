<?php

namespace Givebutter\Tests\Unit\Models;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Givebutter\Tests\Support\Account;
use Givebutter\Tests\TestCase;
use Illuminate\Support\Str;

class ApiKeyTest extends TestCase
{
    /** @test */
    public function create_new_api_key_specifying_the_token(): void
    {
        $account = Account::create();
        $planKey = Str::random(40);

        ApiKey::create([
            'key' => $planKey,
            'keyable_id' => $account->getKey(),
            'keyable_type' => Account::class,
            'name' => 'my api key',
        ]);

        $this->assertDatabaseHas('api_keys', [
            'key' => hash('sha256', $planKey),
            'keyable_id' => $account->getKey(),
            'keyable_type' => Account::class,
            'name' => 'my api key',
        ]);
    }

    /** @test */
    public function create_new_api_key_without_specifying_token(): void
    {
        $account = Account::create();

        ApiKey::create([
            'keyable_id' => $account->getKey(),
            'keyable_type' => Account::class,
            'name' => 'my api key',
        ]);

        $this->assertDatabaseHas('api_keys', [
            'keyable_id' => $account->getKey(),
            'keyable_type' => Account::class,
            'name' => 'my api key',
        ]);
    }
}
