<?php

namespace Givebutter\Tests;

use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Givebutter\LaravelKeyable\KeyableServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $namespace = 'Database\\Factories\\';

            $modelName = Str::afterLast($modelName, '\\');

            return $namespace.$modelName.'Factory';
        });

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            KeyableServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        $this->prepareDatabaseForHasCustomFieldsModel();
        $this->runMigrationStub();
    }

    protected function runMigrationStub()
    {
        include_once __DIR__ . '/../database/migrations/2019_04_09_225232_create_api_keys_table.php';
        (new \CreateApiKeysTable())->up();

        include_once __DIR__ . '/../database/migrations/2023_11_06_095032_increase_key_column_length_add_nullable_name_column_to_api_keys_table.php';
        (new \IncreaseKeyColumnLengthAddNullableNameColumnToApiKeysTable())->up();
    }

    protected function prepareDatabaseForHasCustomFieldsModel()
    {
        include_once __DIR__ . '/../tests/Support/Migrations/create_test_tables.php';
        (new \CreateTestTables())->up();
    }

    protected function resetDatabase()
    {
        $this->artisan('migrate:fresh');
        $this->runMigrationStub();
    }
}
