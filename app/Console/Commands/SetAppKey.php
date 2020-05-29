<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetAppKey extends Command
{
    protected $signature = 'set:appkey';

    protected $description = 'set app key to .env';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $envFile = base_path('.env');
        $appkey = \Illuminate\Support\Str::random(32);

        if (file_exists($envFile)) {
            file_put_contents($envFile, str_replace(
                'APP_KEY=' . env('APP_KEY'), 'APP_KEY=' . $appkey, file_get_contents($envFile)
            ));
        }
    }
}
