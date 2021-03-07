<?php

namespace Givebutter\LaravelKeyable\Console\Commands;

use Givebutter\LaravelKeyable\Models\ApiKey;
use Illuminate\Console\Command;

class DeleteApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:delete
                            {--id= : ID of the API key you want to delete.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete API key';

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
        $key = ApiKey::findOrFail($this->option('id'));
        $key->delete();
        $this->info('API key successfully deleted.');
    }
}
