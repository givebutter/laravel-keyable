<?php

namespace Givebutter\Tests\Feature;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Givebutter\Tests\Support\Account;
use Givebutter\Tests\Support\Post;
use Givebutter\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class CompatibilityMode extends TestCase
{
    /** @test */
    public function accepts_both_hashed_and_non_hashed_api_keys_when_compatibility_mode_is_on()
    {
        Route::get("/api/posts/{post}", function (Request $request, Post $post) {
            return response('All good', 200);
        })->middleware(['api', 'auth.apikey'])->keyableScoped();

        $account = Account::create();
        $post = $account->posts()->create();

        // Store the first api key as non hashed
        $plainTextApiKey1 = ApiKey::generate();
        $apiKey1 = Model::withoutEvents(function () use ($plainTextApiKey1, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $plainTextApiKey1,
            ]);
        });

        // Store the second api key as non hashed
        $plainTextApiKey2 = ApiKey::generate();
        $apiKey2 = Model::withoutEvents(function () use ($plainTextApiKey2, $account) {
            return ApiKey::create([
                'keyable_id' => $account->getKey(),
                'keyable_type' => Account::class,
                'key' => $plainTextApiKey2,
            ]);
        });

        $this->assertDatabaseCount('api_keys', 2);
        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey1->getKey(),
            'key' => $plainTextApiKey1,
        ]);
        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey2->getKey(),
            'key' => $plainTextApiKey2,
        ]);

        // Ensure compatibility mode is on
        Config::set('keyable.compatibility_mode', true);

        // Hash only the second api key
        $this->artisan('api-key:hash', [
            '--id' => $apiKey2->getKey(),
        ]);

        $this->assertDatabaseCount('api_keys', 2);
        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey1->getKey(),
            'key' => $plainTextApiKey1,
        ]);
        $this->assertDatabaseHas('api_keys', [
            'id' => $apiKey2->getKey(),
            'key' => $apiKey2->fresh()->key,
        ]);

        // Assert the non hashed api keys works
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainTextApiKey1,
        ])->get("/api/posts/{$post->id}")->assertOk();

        // Assert the hashed api keys works
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainTextApiKey2,
        ])->get("/api/posts/{$post->id}")->assertOk();
    }
}
