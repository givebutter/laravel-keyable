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
    protected $signature = 'api-key:hash {--id=}';

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
            $id = $this->option('id');

            ApiKey::query()
                ->withTrashed()
                ->when($id, function (Builder $query) use ($id) {
                    $query->where('id', $id);
                })
                ->eachById(function (ApiKey $apiKey) {
                    $apiKey->update([
                        'key' => hash('sha256', $apiKey->key),
                    ]);

                    $this->info("API key of ID {$apiKey->getKey()} successfully hashed.");
                }, 250);

            $this->info('All API keys were successfully hashed.');
        });
    }
}
