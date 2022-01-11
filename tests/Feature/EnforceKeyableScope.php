<?php

namespace Givebutter\Tests\Feature;

use Illuminate\Http\Request;
use Givebutter\Tests\TestCase;
use Givebutter\Tests\Support\Post;
use Givebutter\Tests\Support\Account;
use Illuminate\Support\Facades\Route;
use Givebutter\Tests\Support\PostsController;
use Givebutter\Tests\Support\CommentsController;

class EnforceKeyableScope extends TestCase
{
    /** @test */
    public function request_with_parameter_must_be_owned_by_keyable()
    {
        Route::get("/api/posts/{post}", function (Request $request, Post $post) {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey'])->keyableScoped();

        $account = Account::create();
        $post = $account->posts()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->createApiKey()->key,
        ])->get("/api/posts/{$post->id}")->assertOk();
    }

    /** @test */
    public function request_with_model_not_owned_by_keyable_throws_model_not_found()
    {
        Route::get("/api/posts/{post}", function (Request $request, Post $post) {
            return response('All good', 200);
        })->middleware([ 'api', 'auth.apikey'])->keyableScoped();

        $account = Account::create();
        $account2 = Account::create();
        $post = $account2->posts()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->createApiKey()->key,
        ])->get("/api/posts/{$post->id}")->assertNotFound();
    }

    /** @test */
    public function works_with_resource_routes()
    {
        Route::prefix('api')->middleware(['api', 'auth.apikey'])->group(function () {
            Route::apiResource('posts', PostsController::class)
                ->only('show')
                ->keyableScoped();
        });

        /*
        | --------------------------------
        | PASSING
        | --------------------------------
        */
        $account = Account::create();
        $post = $account->posts()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->createApiKey()->key,
        ])->get("/api/posts/{$post->id}")->assertOk();

        /*
        | --------------------------------
        | FAILING
        | --------------------------------
        */
        $account2 = Account::create();
        $post = $account2->posts()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->createApiKey()->key,
        ])->get("/api/posts/{$post->id}")->assertNotFound();
    }

    /** @test */
    public function can_use_scoped_with_keyableScoped()
    {
        Route::middleware(['api', 'auth.apikey'])->group(function () {
            Route::apiResource('posts.comments', CommentsController::class)
                ->only('show')
                ->scoped()
                ->keyableScoped();
        });

        /*
        | --------------------------------
        | PASSING
        | --------------------------------
        */
        $account = Account::create();
        $post = $account->posts()->create();
        $comment = $post->comments()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->createApiKey()->key,
        ])->get("posts/{$post->id}/comments/{$comment->id}")->assertOk();

        /*
        | --------------------------------
        | FAILING
        | --------------------------------
        */
        $account2 = Account::create();
        $post2 = $account2->posts()->create();
        $comment2 = $post2->comments()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $account->createApiKey()->key,
        ])->get("posts/{$post->id}/comments/{$comment2->id}")->assertNotFound();
    }
}
