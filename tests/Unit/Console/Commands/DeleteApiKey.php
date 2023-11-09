<?php

namespace Givebutter\Tests\Unit\Console\Commands;

use Givebutter\Tests\Support\Account;
use Givebutter\Tests\TestCase;

class DeleteApiKey extends TestCase
{
    /** @test */
    public function delete_api_key(): void
    {
        $account = Account::create();
        $apiKey = $account->apiKeys()->create();

        $this->assertNotSoftDeleted($apiKey);

        $this->artisan('api-key:delete', [
            '--id' => $apiKey->getKey()
        ]);

        $this->assertSoftDeleted($apiKey);
    }
}
