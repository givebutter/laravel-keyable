<?php

namespace Givebutter\Tests\Unit\Console\Commands;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Givebutter\Tests\Support\Account;
use Givebutter\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class HashApiKeys extends TestCase
{
    /** @test */
    public function hash_api_keys(): void
    {
        // Arrange
        $account = Account::create();

        $planTextApiKey1 = ApiKey::generate();
        $apiKeyNotHashed1 = Model::withoutEvents(function () use ($planTextApiKey1, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $planTextApiKey1,
            ]);
        });

        $planTextApiKey2 = ApiKey::generate();
        $apiKeyNotHashed2 = Model::withoutEvents(function () use ($planTextApiKey2, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $planTextApiKey2,
            ]);
        });

        $this->assertDatabaseCount('api_keys', 2);

        $this->assertEquals($planTextApiKey1, $apiKeyNotHashed1->key);
        $this->assertEquals($planTextApiKey2, $apiKeyNotHashed2->key);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed1->id,
            'key' => $planTextApiKey1,
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed2->id,
            'key' => $planTextApiKey2,
        ]);

        // Act
        $this->artisan('api-key:hash');

        // Assert
        $this->assertDatabaseCount('api_keys', 2);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed1->id,
            'key' => hash('sha256', $planTextApiKey1),
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed2->id,
            'key' => hash('sha256', $planTextApiKey2),
        ]);
    }

    /** @test */
    public function hash_one_api_key_at_a_time(): void
    {
        // Arrange
        $account = Account::create();

        $planTextApiKey1 = ApiKey::generate();
        $apiKeyNotHashed1 = Model::withoutEvents(function () use ($planTextApiKey1, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $planTextApiKey1,
            ]);
        });

        $planTextApiKey2 = ApiKey::generate();
        $apiKeyNotHashed2 = Model::withoutEvents(function () use ($planTextApiKey2, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $planTextApiKey2,
            ]);
        });

        $this->assertDatabaseCount('api_keys', 2);

        $this->assertEquals($planTextApiKey1, $apiKeyNotHashed1->key);
        $this->assertEquals($planTextApiKey2, $apiKeyNotHashed2->key);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed1->id,
            'key' => $planTextApiKey1,
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed2->id,
            'key' => $planTextApiKey2,
        ]);

        // Act
        $this->artisan('api-key:hash', [
            '--id' => $apiKeyNotHashed1->id,
        ]);

        // Assert
        $this->assertDatabaseCount('api_keys', 2);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed1->id,
            'key' => hash('sha256', $planTextApiKey1),
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed2->id,
            'key' => $planTextApiKey2,
        ]);
    }
}
