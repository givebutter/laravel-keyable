<?php

namespace Givebutter\LaravelKeyable\Console\Commands;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Illuminate\Console\Command;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:generate
                            {--id= : ID of the model you want to bind to this API key}
                            {--type= : The class name of the model you want to bind to this API key}
                            {--name= : The name you want to give to this API key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API key';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $planTextApiKey = ApiKey::generate();

        $apiKey = (new ApiKey)->create([
            'keyable_id' => $this->option('id'),
            'keyable_type' => $this->option('type'),
            'name' => $this->option('name'),
            'key' => $planTextApiKey,
        ]);

        $this->info('The following API key was created: ' . "{$apiKey->getKey()}|{$planTextApiKey}");
    }
}
