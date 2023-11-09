<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Illuminate\Http\Request;
use Givebutter\Tests\TestCase;
use Givebutter\Tests\Support\Post;
use Givebutter\Tests\Support\Account;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class NonHashedApiKeysMode extends TestCase
{
    /** @test */
    public function control_weather_non_hashed_api_keys_should_be_accepted()
    {
        Route::get("/api/posts/{post}", function (Request $request, Post $post) {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey'])->keyableScoped();

        $account = Account::create();
        $post = $account->posts()->create();

        $plainTextApiKey = ApiKey::generate();

        // Store the api key as non hashed
        DB::table('api_keys')
            ->insert([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $plainTextApiKey,
            ]);

        $this->assertDatabaseCount('api_keys', 1);
        $this->assertDatabaseHas('api_keys', [
            'key' => $plainTextApiKey,
        ]);

        Config::set('keyable.non_hashed_api_keys_mode', true);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainTextApiKey,
        ])->get("/api/posts/{$post->id}")->assertOk();

        $this->artisan('api-key:hash');

        $this->assertDatabaseCount('api_keys', 1);
        $this->assertDatabaseHas('api_keys', [
            'key' => hash('sha256', $plainTextApiKey),
        ]);

        Config::set('keyable.non_hashed_api_keys_mode', false);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainTextApiKey,
        ])->get("/api/posts/{$post->id}")->assertOk();
    }
}