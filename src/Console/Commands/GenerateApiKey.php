<?php

namespace Givebutter\LaravelKeyable\Console\Commands;

use Illuminate\Console\Command;
use Givebutter\LaravelKeyable\Models\ApiKey;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:generate
                            {--id= : ID of the model you want to bind to this API key}
                            {--type= : The class name of the model you want to bind to this API key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API key';

    /**
     * Create a new command instance.
     *
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
        $keyableId = $this->option('id');
        $keyableType = $this->option('type');

        $apiKey = new ApiKey([
            'key'          => ApiKey::generate(),
            'keyable_id'   => $keyableId,
            'keyable_type' => $keyableType,
        ]);

        $apiKey->save();

        $this->info('The following API key was created: ' . $apiKey->key);

        return;
    }
}