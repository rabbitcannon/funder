<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Eos\Common\EmCeeService;

class GiantFoe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'giant:foe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fake Command for Testing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * This demonstrates a mid-execution or periodic schedule update
     * to inform EmCee we are still running but making progress
     *
     * @return mixed
     */
    public function handle()
    {
        sleep(5);
        $mc = new EmCeeService();
        $mc->generateScheduleUpdate('foe',5);
        sleep(5);
        $mc->generateScheduleUpdate('foe',10);
        sleep(5);
        $mc->generateScheduleUpdate('foe',15);
        sleep(5);
        $mc->generateScheduleUpdate('foe','Done!');
        Log::info("giant:foe ran.");
        print("this goes right into the storage/logs/foe.log");
    }
}
