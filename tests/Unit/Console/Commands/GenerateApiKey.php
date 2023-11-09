<?php

namespace Givebutter\Tests\Unit\Console\Commands;

use Givebutter\Tests\Support\Account;
use Givebutter\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class GenerateApiKey extends TestCase
{
    /** @test */
    public function generate_api_key(): void
    {
        // Arrange
        $account = Account::create();

        $this->assertDatabaseEmpty('api_keys');

        // Act
        $this->withoutMockingConsoleOutput()
            ->artisan('api-key:generate', [
                '--id' => $account->getKey(),
                '--type' => Account::class,
                '--name' => 'my api key',
            ]);

        // Assert
        $output = Artisan::output();
        $generatedKey = explode('|', $output, 2)[1];
        $generatedKey = str_replace("\n", '', $generatedKey);

        $this->assertDatabaseHas('api_keys', [
            'key' => hash('sha256', $generatedKey),
            'keyable_id' => $account->getKey(),
            'keyable_type' => Account::class,
            'name' => 'my api key',
        ]);
    }
}
