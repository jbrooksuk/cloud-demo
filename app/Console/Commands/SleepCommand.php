<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SleepCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:sleep';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sleeps for 1 hour';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Sleeping for 1 hour...');

        sleep(3600);

        $this->info('Done sleeping.');
    }
}
