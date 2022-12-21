<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EmptyLogFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        system("echo '' > storage/logs/laravel.log");
        return 0;
    }
}
