<?php

namespace Givebutter\LaravelKeyable\Console\Commands;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class HashApiKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:hash {--id= : ID of the API key you want to hash}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash existing API keys';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::transaction(function () {
            ApiKey::query()
                ->withTrashed()
                ->when($this->option('id'), function (Builder $query, int $id) {
                    $query->where('id', $id);
                })
                ->eachById(function (ApiKey $apiKey) {
                    $apiKey->update([
                        'key' => hash('sha256', $apiKey->key),
                    ]);

                    $this->info("API key #{$apiKey->getKey()} successfully hashed.");
                }, 250);

            $this->info('All API keys were successfully hashed.');
        });
    }
}
