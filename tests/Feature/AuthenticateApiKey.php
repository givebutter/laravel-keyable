<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelKeyable\Exceptions\ForbidenRequestParamException;
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
    public function request_with_api_key_responds_ok_in_param_mode()
    {
        Route::get("/api/posts", function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey:parameter']);

        $account = Account::create();

        $this->get("/api/posts?api_key=" . $account->createApiKey()->plainTextApiKey)->assertOk();
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

    /**
     * @test
     * @dataProvider forbiddenRequestParams
     */
    public function throw_exception_if_unauthorized_get_request_has_forbidden_request_query_params(string $queryParam): void
    {
        Route::get('/api/posts', function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $this->get("/api/posts?{$queryParam}=value")
            ->assertBadRequest()
            ->assertContent("Request param '{$queryParam}' is not allowed.");
    }

    /**
     * @test
     * @dataProvider forbiddenRequestParams
     */
    public function throw_exception_if_unauthorized_post_request_has_forbidden_request_body_params(string $bodyParam): void
    {
        Route::post('/api/posts', function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $this->post('/api/posts', [$bodyParam => 'value'])
            ->assertBadRequest()
            ->assertContent("Request param '{$bodyParam}' is not allowed.");
    }

    /**
     * @test
     * @dataProvider forbiddenRequestParams
     */
    public function throw_exception_if_unauthorized_json_get_request_has_forbidden_request_query_params(string $queryParam): void
    {
        Route::get('/api/posts', function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $this->getJson("/api/posts?{$queryParam}=value")
            ->assertBadRequest()
            ->assertJson(['message' => "Request param '{$queryParam}' is not allowed."]);
    }

    /**
     * @test
     * @dataProvider forbiddenRequestParams
     */
    public function throw_exception_if_unauthorized_json_post_request_has_forbidden_request_body_params(string $bodyParam): void
    {
        Route::post('/api/posts', function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $this->postJson('/api/posts', [$bodyParam => 'value'])
            ->assertBadRequest()
            ->assertJson(['message' => "Request param '{$bodyParam}' is not allowed."]);
    }

    public function forbiddenRequestParams(): array
    {
        return [
            ['keyable'],
            ['apiKey'],
        ];
    }

    /** @test */
    public function request_without_api_key_properly_set_responds_unauthorized()
    {
        Route::get("/api/posts", function () {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey:parameter']);

        $account = Account::create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->createApiKey()->plainTextApiKey,
        ])->get("/api/posts")->assertUnauthorized();
    }
}
