<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class JobUnFreezeCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:job-un-freeze-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('New Cron job is running');
    }
}
