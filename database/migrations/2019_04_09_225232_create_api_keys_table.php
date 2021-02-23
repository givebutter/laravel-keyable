<?php

use Givebutter\LaravelKeyable\Exception\ConfigException;
use Givebutter\LaravelKeyable\Models\ApiKey;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $this->generateIdentifiers($table);
            $table->string('key', 40);
            $table->dateTime('last_used_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('key');
        });
    }

    /**
     * @param Blueprint $table
     */
    public function generateIdentifiers(Blueprint $table): void
    {
        $identifier = Config::get('keyable.identifier');

        switch ($identifier) {
            case 'bigint':
                $table->increments('id');
                $table->nullableMorphs('keyable');
                break;
            case 'uuid':
                $table->uuid('id')->primary();
                $table->nullableUuidMorphs('keyable');
                break;
            default:
                throw ConfigException::withUnsupportedConfig(
                    'keyable.identifier',
                    $identifier
                );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_keys');
    }
}
