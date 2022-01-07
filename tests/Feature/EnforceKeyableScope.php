<?php

namespace Givebutter\Tests\Feature;

use Illuminate\Http\Request;
use Givebutter\Tests\TestCase;
use Givebutter\Tests\Support\Post;
use Givebutter\Tests\Support\Account;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EnforceKeyableScope extends TestCase
{
    /** @test */
    public function request_with_parameter_must_be_owned_by_keyable()
    {
        $account = Account::create();
        $account->createApiKey();

        $post = $account->posts()->create();

        Route::get("/api/posts/{post}", function (Request $request, Post $post) {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey'])->keyableScoped();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->apiKeys()->first()->key,
        ])->get("/api/posts/{$post->id}")->assertOk();
    }

    /** @test */
    public function request_with_model_not_owned_by_keyable_throws_model_not_found()
    {
        $account = Account::create();
        $account->createApiKey();

        $account2 = Account::create();
        $post = $account2->posts()->create();

        Route::get("/api/posts/{post}", function (Request $request, Post $post) {
            return response('All good', 200);
        })->middleware([ 'api', 'auth.apikey'])->keyableScoped();

        try {
            $this->withHeaders([
                'Authorization' => 'Bearer ' . $account->apiKeys()->first()->key,
            ])->get("/api/posts/{$post->id}");
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
            return;
        }

        // force a fail since it shouldn't reach this point.
        $this->assertTrue(false);
    }
}
