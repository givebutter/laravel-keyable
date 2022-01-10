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
            'Authorization' => 'Bearer ' . $account->createApiKey()->key,
        ])->get("/api/posts")->assertOk();
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
