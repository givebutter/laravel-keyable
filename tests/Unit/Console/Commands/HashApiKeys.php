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

        $plainTextApiKey1 = ApiKey::generate();
        $apiKeyNotHashed1 = Model::withoutEvents(function () use ($plainTextApiKey1, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $plainTextApiKey1,
            ]);
        });

        $plainTextApiKey2 = ApiKey::generate();
        $apiKeyNotHashed2 = Model::withoutEvents(function () use ($plainTextApiKey2, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $plainTextApiKey2,
            ]);
        });

        $this->assertDatabaseCount('api_keys', 2);

        $this->assertEquals($plainTextApiKey1, $apiKeyNotHashed1->key);
        $this->assertEquals($plainTextApiKey2, $apiKeyNotHashed2->key);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed1->id,
            'key' => $plainTextApiKey1,
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed2->id,
            'key' => $plainTextApiKey2,
        ]);

        // Act
        $this->artisan('api-key:hash');

        // Assert
        $this->assertDatabaseCount('api_keys', 2);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed1->id,
            'key' => hash('sha256', $plainTextApiKey1),
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed2->id,
            'key' => hash('sha256', $plainTextApiKey2),
        ]);
    }

    /** @test */
    public function hash_one_api_key_at_a_time(): void
    {
        // Arrange
        $account = Account::create();

        $plainTextApiKey1 = ApiKey::generate();
        $apiKeyNotHashed1 = Model::withoutEvents(function () use ($plainTextApiKey1, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $plainTextApiKey1,
            ]);
        });

        $plainTextApiKey2 = ApiKey::generate();
        $apiKeyNotHashed2 = Model::withoutEvents(function () use ($plainTextApiKey2, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $plainTextApiKey2,
            ]);
        });

        $this->assertDatabaseCount('api_keys', 2);

        $this->assertEquals($plainTextApiKey1, $apiKeyNotHashed1->key);
        $this->assertEquals($plainTextApiKey2, $apiKeyNotHashed2->key);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed1->id,
            'key' => $plainTextApiKey1,
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed2->id,
            'key' => $plainTextApiKey2,
        ]);

        // Act
        $this->artisan('api-key:hash', [
            '--id' => $apiKeyNotHashed1->id,
        ]);

        // Assert
        $this->assertDatabaseCount('api_keys', 2);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed1->id,
            'key' => hash('sha256', $plainTextApiKey1),
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKeyNotHashed2->id,
            'key' => $plainTextApiKey2,
        ]);
    }

    /** @test */
    public function api_key_is_not_hashed_more_than_once(): void
    {
        // Arrange
        $account = Account::create();

        $plainTextApiKey = ApiKey::generate();
        $apiKey = Model::withoutEvents(function () use ($plainTextApiKey, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $plainTextApiKey,
            ]);
        });

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey->id,
            'key' => $plainTextApiKey,
        ]);

        // Act 1
        $this->artisan('api-key:hash', [
            '--id' => $apiKey->id,
        ]);

        // Assert 1
        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey->id,
            'key' => hash('sha256', $plainTextApiKey),
        ]);

        // Act 2
        $this->artisan('api-key:hash', [
            '--id' => $apiKey->id,
        ]);

        // Assert 2
        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey->id,
            'key' => hash('sha256', $plainTextApiKey),
        ]);
    }
}
