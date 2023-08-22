<?php

namespace App\Console\Commands;

use Database\Create\Database;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DropCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:shard-drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop many databases by shard follow config';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $result = Database::dropDatabase();
            $this->info($result);
            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            return Command::FAILURE;
        }
    }
}
