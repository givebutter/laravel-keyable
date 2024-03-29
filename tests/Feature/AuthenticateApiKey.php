<?php

namespace Givebutter\Tests\Feature;

use Givebutter\Tests\TestCase;
use Givebutter\Tests\Support\Account;
use Illuminate\Support\Facades\Route;

class AuthenticateApiKey extends TestCase
{
    /** @test */
    public function request_with_api_key_responds_ok()
    {
        Route::get("/api/posts", function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $account = Account::create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->createApiKey()->plainTextApiKey,
        ])->get("/api/posts")->assertOk();
    }

    /** @test */
    public function request_with_valid_api_key_without_id_prefix_responds_ok()
    {
        Route::get("/api/posts", function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $account = Account::create();
        $plainTextApiKey = $account->createApiKey()->plainTextApiKey;
        [$id, $apiKeyWithoutIdPrefix] = explode('|', $plainTextApiKey);

        $this->assertEquals("{$id}|{$apiKeyWithoutIdPrefix}", $plainTextApiKey);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $apiKeyWithoutIdPrefix,
        ])->get("/api/posts")->assertOk();
    }

    /** @test */
    public function request_having_api_key_with_valid_but_mismatched_id_and_key_responds_unauthorized()
    {
        Route::get("/api/posts", function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $account = Account::create();
        $apiKey1 = $account->createApiKey();
        $apiKey2 = $account->createApiKey();

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey1->apiKey->id,
        ]);

        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey2->apiKey->id,
        ]);

        $idFromApiKey1 = explode('|', $apiKey1->plainTextApiKey)[0];
        $keyFromApiKey2 = explode('|', $apiKey2->plainTextApiKey)[1];

        $mismatchedApiKey = "{$idFromApiKey1}|{$keyFromApiKey2}";

        $this->assertNotEquals($mismatchedApiKey, $apiKey1->plainTextApiKey);
        $this->assertNotEquals($mismatchedApiKey, $apiKey2->plainTextApiKey);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $mismatchedApiKey,
        ])->get("/api/posts")->assertUnauthorized();
    }

    /** @test */
    public function request_without_api_key_responds_unauthorized()
    {
        Route::get("/api/posts", function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $this->get("/api/posts")->assertUnauthorized();
    }
}
