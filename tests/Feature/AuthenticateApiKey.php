<?php

namespace Givebutter\Tests\Feature;

use Illuminate\Http\Request;
use Givebutter\Tests\TestCase;
use Givebutter\Tests\Support\Account;
use Illuminate\Support\Facades\Route;

class AuthenticateApiKey extends TestCase
{
    /** @test */
    public function request_with_api_key_responds_ok()
    {
        $account = Account::create();
        $account->createApiKey();

        Route::get("/api/posts", function (Request $request) {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->apiKeys()->first()->key,
        ])->get("/api/posts")->assertOk();
    }

    /** @test */
    public function request_without_api_key_responds_unauthorized()
    {
        $account = Account::create();
        $account->createApiKey();

        Route::get("/api/posts", function (Request $request) {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey']);

        $this->get("/api/posts")->assertUnauthorized();
    }
}
