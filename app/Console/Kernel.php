<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Eos\Common\ScheduleGenerator;
use Cron\CronExpression;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     * You still need to provide all the scheduled commands you can generate
     * here, even though ScheduleGenerator will do your actual scheduling.
     * This includes things like Eos\Common\Console\Commands\Archivist::class
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\GiantFee::class,
        \App\Console\Commands\GiantFie::class,
        \App\Console\Commands\GiantFoe::class,
        \App\Console\Commands\GiantFum::class
    ];

    /**
     * Define the application's command schedule.
     * Rather than using the familiar mechanisms like:
     * $schedule->command('cheque:request --state=failed -v')
     *   ->dailyAt('2:40')
     *   ->withoutOverlapping();
     * we now have a ScheduleGenerator to pull the commands and schedule
     * specifics from Eos\Common\Setting('eos.schedule'). You will now define
     * all known schedules in a config/eos-schedule.php file (see example)
     * and they can be adjusted on-the-fly with EmCee.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // all we need is this one line. See eos-common wiki 'Schedule generator'
        ScheduleGenerator::scheduleAll($schedule);
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
